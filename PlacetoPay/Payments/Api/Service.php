<?php

namespace PlacetoPay\Payments\Api;

use Dnetix\Redirection\Exceptions\PlacetoPayException;
use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use PlacetoPay\Payments\Helper\PlacetoPayLogger;
use PlacetoPay\Payments\Model\PaymentMethod;

class Service implements ServiceInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var PlacetoPayLogger
     */
    protected $logger;

    /**
     * @var EventManager
     */
    protected $manager;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    public function __construct(
        Request $request,
        PlacetoPayLogger $logger,
        EventManager $manager,
        Json $json,
        OrderRepository $orderRepository,
        ResourceConnection $resource
    ) {
        $this->logger = $logger;
        $this->manager = $manager;
        $this->json = $json;
        $this->orderRepository = $orderRepository;
        $this->request = $request;
        $this->resource = $resource;
    }

    public function notify(): array
    {
        $data = $this->getRequestData($this->request->getContent());

        try {
            if (!$this->isValidRequest($data)) {
                throw new PlacetoPayException('Wrong or empty notification data.');
            }

            $tableName = $this->resource->getTableName('sales_order_payment');

            $orderId = $this->resource->getConnection()->query(
                "SELECT parent_id FROM $tableName WHERE method = ? AND last_trans_id = ?",
                [PaymentMethod::CODE, (string)$data['requestId']]
            )->fetchColumn();

            if (!$orderId) {
                return [
                    'message' => 'There is no order associated to the request id: ' . $data['requestId'],
                ];
            }

            /** @var Order $order */
            $order = $this->getOrderById($orderId);

            /** @var Order\Payment $payment */
            $payment = $order->getPayment();

            /** @var PaymentMethod $placetopay */
            $placetopay = $payment->getMethodInstance();

            $notification = $placetopay->gateway()->readNotification($data);

            if (!$notification->isValidNotification()) {
                return $placetopay->inDebugMode()
                    ? [
                        'signature' => $notification->makeSignature(),
                        'message' => 'Replace this signature with the one on the request body for testing.',
                    ]
                    : [
                        'message' => 'Invalid notification for order #' . $order->getId(),
                    ];
            }

            $information = $placetopay->gateway()->query($notification->requestId());

            $placetopay->setStatus($information, $order);

            if ($information->isApproved()) {
                $this->manager->dispatch('placetopay_api_success', [
                    'order_ids' => [$order->getRealOrderId()],
                ]);

                $response = [
                    'message' => sprintf('Transaction with status: %s', $information->status()->status()),
                ];
            } else {
                if ($information->lastApprovedTransaction() && $information->lastApprovedTransaction()->refunded()) {
                    $response = [
                        'message' => 'The payment refunded',
                    ];
                } else {
                    $response = [
                        'message' => sprintf('Transaction with status: %s', $information->status()->status()),
                    ];
                }
            }
        } catch (Exception $ex) {
            $this->logger->log($this, 'error', __FUNCTION__ . ' message', [$ex->getMessage()]);

            $response = [
                'message' => $ex->getMessage(),
            ];
        }

        return $response;
    }

    /**
     * @throws InputException
     * @throws NoSuchEntityException
     */
    private function getOrderById(int $id): OrderInterface
    {
        return $this->orderRepository->get($id);
    }

    private function isValidRequest(array $data): bool
    {
        return $data
            && !empty($data['reference'])
            && !empty($data['signature'])
            && !empty($data['requestId']);
    }

    private function getRequestData(string $data): array
    {
        return $this->json->unserialize($data);
    }
}
