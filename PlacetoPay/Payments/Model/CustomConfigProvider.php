<?php

namespace PlacetoPay\Payments\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use PlacetoPay\Payments\Helper\Data;
use PlacetoPay\Payments\Model\Adminhtml\Source\Country;

class CustomConfigProvider implements ConfigProviderInterface
{
    public const CODE = PaymentMethod::CODE;

    /**
     * @var Data
     */
    protected $_scopeConfig;

    /**
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        Data $scopeConfig,
        Repository $assetRepo,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        CollectionFactory $collectionFactory
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_assetRepo = $assetRepo;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array[][]
     * @throws NoSuchEntityException
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                self::CODE => [
                    'media' => $this->_assetRepo->getUrl('PlacetoPay_Payments::images'),
                    'logoUrl' => $this->_assetRepo->getUrl('PlacetoPay_Payments::images/logo.png'),
                    'logo' => $this->getImage(),
                    'legalName' => $this->_scopeConfig->getLegalName(),
                    'order' => $this->getLastOrder(),
                    'hasCifinMessage' => $this->_scopeConfig->getHasCifin(),
                    'allowPendingPayments' => $this->_scopeConfig->getAllowPendingPayment(),
                    'minimum' => $this->_scopeConfig->getMinimumAmount(),
                    'maximum' => $this->_scopeConfig->getMaximumAmount(),
                    'url' => $this->getUrl(),
                    'paymentMethods' => [],
                ],
            ],
        ];
    }

    /**
     * @throws NoSuchEntityException
     */
    protected function getImage(): string
    {
        $url = $this->_scopeConfig->getImageUrl();

        if (is_null($url)) {
            switch ($this->_scopeConfig->getCountryCode()) {
                case Country::CHILE:
                    $image = 'https://banco.santander.cl/uploads/000/029/870/0620f532-9fc9-4248-b99e-78bae9f13e1d/original/Logo_WebCheckout_Getnet.svg';
                    break;
                case Country::COLOMBIA:
                case Country::ECUADOR:
                case Country::PUERTO_RICO:
                case Country::COSTA_RICA:
                default:
                    $image = 'https://static.placetopay.com/placetopay-logo.svg';
            }
        } elseif ($this->checkValidUrl($url)) {
            $image = $url;
        } elseif ($this->checkDirectory($url)) {
            $base = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            $image = "{$base}{$url}";
        } else {
            $image = 'https://static.placetopay.com/' . $url . '.svg';
        }

        return $image;
    }

    protected function checkDirectory(string $path): bool
    {
        return substr($path, 0, 1) === '/';
    }

    protected function checkValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @return array|false[]
     */
    public function getLastOrder(): array
    {
        $data = ['hasPendingOrder' => false];
        $customerId = $this->customerSession->getCustomer()->getId();

        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('customer_id', $customerId)
            ->addAttributeToFilter('state', ['in' => [
                Order::STATE_PENDING_PAYMENT,
                Order::STATE_NEW,
            ]])
            ->addAttributeToFilter('status', ['in' => [
                'pending_payment',
            ]])
            ->addAttributeToSort('created_at', 'DESC')
            ->load()
            ->getItems();

        $pendingOrders = count($collection);

        if ($pendingOrders > 0) {
            $lastOrder = reset($collection);
            $information = $lastOrder->getPayment()->getAdditionalInformation();

            $data = [
                'hasPendingOrder' => true,
                'id' => $lastOrder->getRealOrderId(),
                'phone' => $this->_scopeConfig->getPhone(),
                'email' => $this->_scopeConfig->getEmail(),
                'authorization' => $information['authorization'] ?? null,
            ];
        }

        return $data;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB, true);
    }
}
