<?php

namespace PlacetoPay\Payments\Controller\Payment;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use PlacetoPay\Payments\Logger\Logger as LoggerInterface;
use PlacetoPay\Payments\Model\PaymentMethod;

/**
 * Class Data.
 */
class Data extends Action
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var ResultFactory
     */
    protected $resultRedirect;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        LoggerInterface $logger,
        ManagerInterface $messageManager,
        JsonFactory $jsonFactory,
        ResultFactory $result,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);

        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->jsonFactory = $jsonFactory;
        $this->resultRedirect = $result;
        $this->orderRepository = $orderRepository;
    }

    protected function _getCheckout(): Session
    {
        return $this->checkoutSession;
    }

    /**
     * When the user clicks on the proceed to payment button.
     *
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $session = $this->_getCheckout();

        try {
            /**
             * @var Order
             */
            $order = $session->getLastRealOrder();

            if (! $order->getId()) {
                $this->logger->debug('Non existent order for reference #' . $order->getId());

                throw new LocalizedException(__('No order for processing was found.'));
            }

            /**
             * @var PaymentMethod
             */
            $placetopay = $order->getPayment()->getMethodInstance();
            $url = $placetopay->getCheckoutRedirect($order);

            $order->setStatus('pending');
            $order->setState(Order::STATE_PENDING_PAYMENT);
            $this->orderRepository->save($order);

            $result = $this->jsonFactory->create();

            return $result->setData([
                'url' => $url,
            ]);
        } catch (Exception $exception) {
            $session->restoreQuote();

            $this->logger->debug(
                'Redirect action ' .
                $exception->getMessage() . ' on ' .
                $exception->getFile() . ' line ' .
                $exception->getLine()
            );

            $this->messageManager->addErrorMessage($exception->getMessage());

            $result = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);

            $result->setUrl($this->_redirect->getRefererUrl());

            return $result;
        }
    }
}
