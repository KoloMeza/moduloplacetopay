<?php

namespace PlacetoPay\Payments\PlacetoPayService;

use Dnetix\Redirection\Message\RedirectInformation;
use Dnetix\Redirection\PlacetoPay;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Header;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Tax\Item;
use PlacetoPay\Payments\Concerns\IsSetStatusOrderTrait;
use PlacetoPay\Payments\Exception\PlacetoPayException;
use PlacetoPay\Payments\Helper\Data as Config;
use PlacetoPay\Payments\Helper\DebugLogger;
use PlacetoPay\Payments\Helper\OrderHelper;
use PlacetoPay\Payments\Logger\Logger as LoggerInterface;

class PlacetoPayPayment
{
    use IsSetStatusOrderTrait;

    /**
     * @var PlacetoPay
     */
    protected $gateway;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var Header
     */
    private $header;

    /**
     * @var Item
     */
    private $item;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var DebugLogger
     */
    protected $debugLogger;

    public function __construct(
        Config $config,
        LoggerInterface $logger,
        Resolver $resolver,
        UrlInterface $url,
        RemoteAddress $remoteAddress,
        Header $header,
        Item $item
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->resolver = $resolver;
        $this->url = $url;
        $this->remoteAddress = $remoteAddress;
        $this->header = $header;
        $this->item = $item;
        $this->uuid = uniqid();

        $this->debugLogger = new DebugLogger($this->uuid, $this->logger);
        $settings = [
            'login' => $config->getLogin(),
            'tranKey' => $config->getTranKey(),
            'baseUrl' => $config->getUri(),
            'headers' => $config->getHeaders(),
        ];

        $this->gateway = new PlacetoPay($settings);
    }

    /**
     * @throws Exception
     */
    public function getCheckoutRedirect(Order $order): ?string
    {
        try {
            $this->debugLogger->logInfo('Initialize redirection to checkout to order', ['id' => $order->getId()] );

            $response = $this->gateway()->request($this->getRedirectRequestData($order));

            $this->debugLogger->logInfo('Response of checkout', ['data' => $response->toArray()]);

            if (!$response->isSuccessful()) {
                $this->logger->debug(
                    'Payment error [' .
                    $order->getRealOrderId() . '] ' .
                    $response->status()->message() . ' - ' .
                    $response->status()->reason() . ' ' .
                    $response->status()->status()
                );

                throw new LocalizedException(__($response->status()->message()));
            }

            $this
                ->config
                ->getInfoModel()
                ->loadInformationFromRedirectResponse(
                    $order->getPayment(),
                    $response,
                    $this->config->getMode(),
                    $order,
                    $this->debugLogger
                );

            return $response->processUrl();
        } catch (\Throwable $ex) {
            $this->logger->error(
                'Payment error [' .
                $order->getRealOrderId() . '] ' .
                $ex->getMessage() . ' on ' . $ex->getFile() . ' line ' .
                $ex->getLine() . ' - ' . \get_class($ex)
            );

            $this->logger->error('The order ' . $order->getRealOrderId() . ' has a problem to create the payment');

            throw new PlacetoPayException(__('Something went wrong with your request. Please try again later.'));
        }
    }

    public function resolve(Order $order, Order\Payment $payment = null): RedirectInformation
    {
        if (!$payment) {
            $payment = $order->getPayment();
        }

        $info = $payment->getAdditionalInformation();

        if (!$info || !isset($info['request_id'])) {
            $this->logger->debug('No additional information for order: ' . $order->getRealOrderId());

            throw new LocalizedException(__('No additional information for order: %1', $order->getRealOrderId()));
        }

        $response = $this->gateway->query($info['request_id']);

        if ($response->isSuccessful()) {
            $this->logger->info(
                'The payment ' . $response->requestId() . ' was ' . $response->status()->status()
                . ' processing to resolve the payment'
            );

            $this->setStatus($response, $order, $payment);
        } else {
            $this->logger->info(
                'The payment: ' . $response->requestId() . ' was ' . $response->status()->status()
                . ' ' . $response->status()->message() . ' ' . $response->status()->reason()
            );
        }

        return $response;
    }

    public function consultTransactionInfo(string $requestId): RedirectInformation
    {
        return $this->gateway->query($requestId);
    }

    public function gateway(): PlacetoPay
    {
        return $this->gateway;
    }

    private function getRedirectRequestData(Order $order): array
    {
        $reference = $order->getRealOrderId();
        $total = !\is_null($order->getGrandTotal()) ? $order->getGrandTotal() : $order->getTotalDue();
        $subtotal = $order->getSubtotal();
        $discount = (string)$order->getDiscountAmount() != 0 ? ($order->getDiscountAmount() * -1) : 0;
        $shipping = $order->getShippingAmount();
        $visibleItems = $order->getAllVisibleItems();
        $expiration = date('c', strtotime($this->config->getExpirationTimeMinutes() . ' minutes'));
        $items = [];

        /** @var Order\Item $item */
        foreach ($visibleItems as $item) {
            $items[] = [
                'sku' => $item->getSku(),
                'name' => $this->config->cleanText($item->getName()),
                'category' => $item->getProductType(),
                'qty' => $item->getQtyOrdered(),
                'price' => $item->getPrice(),
                'tax' => $item->getTaxAmount(),
            ];
        }

        $data = [
            'locale' => $this->resolver->getLocale(),
            'buyer' => OrderHelper::parseAddressPerson($order->getBillingAddress()),
            'payment' => [
                'reference' => $reference,
                'description' => 'Pedido ' . $order->getId(),
                'amount' => [
                    'details' => [
                        [
                            'kind' => 'subtotal',
                            'amount' => $subtotal,
                        ],
                        [
                            'kind' => 'discount',
                            'amount' => (string)$discount,
                        ],
                        [
                            'kind' => 'shipping',
                            'amount' => $shipping,
                        ],
                    ],
                    'currency' => $order->getOrderCurrencyCode(),
                    'total' => $total,
                ],
                'items' => $items,
                'shipping' => OrderHelper::parseAddressPerson($order->getShippingAddress()),
                'allowPartial' => $this->config->getAllowPartialPayment(),
            ],
            'returnUrl' => $this->url->getUrl('placetopay/payment/response', ['reference' => $reference]),
            'expiration' => $expiration,
            'ipAddress' => $this->remoteAddress->getRemoteAddress(),
            'userAgent' => $this->header->getHttpUserAgent(),
            'skipResult' => $this->config->getSkipResult(),
            'noBuyerFill' => $this->config->getFillBuyerInformation(),
        ];

        if ($this->config->getFillTaxInformation()) {
            $data['payment']['amount']['taxes'] = $this->getPaymentTaxes($order);
        }

        return $data;
    }

    private function getPaymentTaxes(Order $order): array
    {
        $mergedTaxes = [];

        try {
            $map = [];

            if ($mapping = $this->config->getTaxRateParsing()) {
                foreach (explode('|', $mapping) as $item) {
                    $type = explode(':', $item);

                    if (\is_array($type) && \count($type) == 2) {
                        $map[$type[0]] = $type[1];
                    }
                }
            }

            $taxInformation = $this->item->getTaxItemsByOrderId($order->getId());

            if (\is_array($taxInformation) && \count($taxInformation) > 0) {
                $taxes = [];

                foreach ($taxInformation as $item) {
                    $base = isset($item['item_id']) ? $order->getItemById($item['item_id'])->getBasePrice() :
                        ($item['real_amount'] * 100) / $item['tax_percent'];

                    $taxes[] = [
                        'kind' => $map[$item['code']] ?? 'valueAddedTax',
                        'amount' => $item['real_amount'],
                        'base' => $base,
                    ];
                }

                foreach ($taxes as $elem) {
                    $mergedTaxes[$elem['kind']]['kind'] = $elem['kind'];

                    $mergedTaxes[$elem['kind']]['amount'] = isset($mergedTaxes[$elem['kind']]['amount'])
                        ? $this->getFormatAmount((float)$mergedTaxes[$elem['kind']]['amount'] + (float)$elem['amount'])
                        : $this->getFormatAmount((float)$elem['amount']);

                    $mergedTaxes[$elem['kind']]['base'] = isset($mergedTaxes[$elem['kind']]['base'])
                        ? $this->getFormatAmount((float)$mergedTaxes[$elem['kind']]['base'] + (float)$elem['base'])
                        : $this->getFormatAmount((float)$elem['base']);
                }

                return array_values($mergedTaxes);
            }
        } catch (Exception $ex) {
            $this->logger->debug(
                'Error calculating taxes: [' .
                $order->getRealOrderId() .
                '] ' . serialize($this->item->getTaxItemsByOrderId($order->getId()))
            );
        }

        return $mergedTaxes;
    }

    protected function logDebug(string $message, array $data): void
    {
        $this->logger->info($this->uuid . $message, $data );
    }

    private function getFormatAmount(float $amount): string
    {
        return number_format((float)$amount, 4, '.', '');
    }
}
