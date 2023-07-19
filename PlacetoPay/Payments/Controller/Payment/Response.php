<?php

namespace PlacetoPay\Payments\Controller\Payment;

use Dnetix\Redirection\Entities\Status;
use Exception;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\ScopeInterface;
use PlacetoPay\Payments\Actions\SetOrderInfoSession;
use PlacetoPay\Payments\Constants\PathUrlRedirect;
use PlacetoPay\Payments\Helper\OrderHelper;
use PlacetoPay\Payments\Helper\PlacetoPayLogger;
use PlacetoPay\Payments\Model\PaymentMethod;

/**
 * Class Response.
 */
class Response extends Action
{
    /**
     * @var PlacetoPayLogger
     */
    protected $_logger;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var OrderFactory
     */
    protected $salesOrderFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var QuoteFactory
     */
    protected $quoteQuoteFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var PaymentHelper
     */
    protected $_paymentHelper;

    /**
     * @var TransactionRepositoryInterface
     */
    protected $_transactionRepository;

    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $salesOrderFactory,
        ManagerInterface $messageManager,
        PlacetoPayLogger $logger,
        Http $request,
        ScopeConfigInterface $scopeConfig,
        QuoteFactory $quoteQuoteFactory,
        CustomerSession $customerSession,
        EventManager $eventManager,
        TransactionRepositoryInterface $transactionRepository,
        PageFactory $pageFactory
    ) {
        $this->_logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->salesOrderFactory = $salesOrderFactory;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->quoteQuoteFactory = $quoteQuoteFactory;
        $this->customerSession = $customerSession;
        $this->eventManager = $eventManager;
        $this->_transactionRepository = $transactionRepository;
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $reference = $this->request->getParam('reference');
        $order = $this->salesOrderFactory->create()->loadByIncrementId($reference);
        $session = $this->checkoutSession;
        $pathRedirect = PathUrlRedirect::SUCCESSFUL;

        try {
            $payment = $order->getPayment();

            /** @var PaymentMethod $placetopay */
            $placetopay = $payment->getMethodInstance();

            if (strcmp($placetopay->getCode(), 'placetopay') !== 0) {
                throw new LocalizedException(__('Unknown payment method.'));
            }

            if (OrderHelper::isPendingOrder($order)) {
                $status = OrderHelper::getPaymentStatus($placetopay->resolve($order, $payment));
            } else {
                $status = OrderHelper::parseOrderState($order);
            }

            /** @var Transaction $transaction */
            $transaction = $this->_transactionRepository->getByTransactionType(
                Transaction::TYPE_ORDER,
                $payment->getId()
            );

            $path = $this->scopeConfig->getValue(
                'payment/' . $placetopay->getCode() . '/final_page',
                ScopeInterface::SCOPE_STORE,
                $order->getStore()
            );
            if (strcmp($path, 'magento_default') === 0) {
                if ($status->status() == Order::STATE_COMPLETE) {
                    $this->messageManager->addSuccessMessage(
                        sprintf(__('Thanks, transaction approved by %s.'), $placetopay->getNameOfStore())
                    );
                } elseif ($status->isApproved()) {
                    $this->setPaymentApproved($payment, $transaction);

                    SetOrderInfoSession::withQouteId($session, $order);
                    $session->setLastSuccessQuoteId($order->getQuoteId());

                    $this->messageManager->addSuccessMessage(
                        sprintf(__('Thanks, transaction approved by %s.'), $placetopay->getNameOfStore())
                    );
                } elseif ($status->isRejected()) {
                    $this->setPaymentDenied($payment, $transaction);

                    $quote = $this->quoteQuoteFactory->create()->load($order->getQuoteId());

                    if ($quote->getId()) {
                        $quote->setIsActive(true)->save();

                        SetOrderInfoSession::withQouteId($session, $order);
                    }

                    $this->messageManager->addErrorMessage(__('The payment process has been declined.'));

                    $pathRedirect = PathUrlRedirect::FAILURE;
                } elseif ($status->status() == Status::ST_REFUNDED) {
                    $this->setPaymentDenied($payment, $transaction);

                    $quote = $this->quoteQuoteFactory->create()->load($order->getQuoteId());

                    if ($quote->getId()) {
                        $quote->setIsActive(true)->save();

                        SetOrderInfoSession::withQouteId($session, $order);
                    }
                    $this->messageManager->addErrorMessage(__('The payment has been refunded.'));
                    $pathRedirect = PathUrlRedirect::FAILURE;
                } else {
                    SetOrderInfoSession::withQouteId($session, $order, false);

                    $this->messageManager->addWarningMessage(
                        __('Transaction pending, please wait a moment while it automatically resolves.')
                    );

                    $pathRedirect = PathUrlRedirect::PENDING;
                }
            } else {
                if ($status->isApproved()) {
                    $this->messageManager->addSuccessMessage(
                        sprintf(__('Thanks, transaction approved by ½s.'), $placetopay->getNameOfStore())
                    );
                    $this->setPaymentApproved($payment, $transaction);
                } elseif ($status->isRejected()) {
                    $this->messageManager->addErrorMessage(__('The payment process has been declined.'));
                    $this->setPaymentDenied($payment, $transaction);
                } elseif ($status->status() == Status::ST_REFUNDED) {
                    $this->messageManager->addErrorMessage(__('The payment process has been refunded.'));
                    $this->setPaymentDenied($payment, $transaction);
                } else {
                    $this->messageManager->addWarningMessage(
                        __('Transaction pending, please wait a moment while it automatically resolves.')
                    );
                }

                if ($this->customerSession->isLoggedIn()) {
                    $this->eventManager->dispatch(
                        'checkout_onepage_controller_success_action',
                        ['order_ids' => [$order->getRealOrderId()]]
                    );

                    $pathRedirect = 'sales/order/view/order_id/' . $order->getRealOrderId();
                } else {
                    $pathRedirect = 'sales/guest/form/';
                }
            }

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            $resultRedirect->setPath($pathRedirect);

            return $resultRedirect;
        } catch (LocalizedException $exception) {
            $this->_logger->log($this, 'error', __FUNCTION__ . ' error', [
                'response' => $exception->getMessage(),
                'code_exception' => $exception->getCode(),
                'file_exception' => $exception->getFile(),
                'line_exception' => $exception->getLine(),
            ]);

            $this->messageManager->addErrorMessage($exception->getMessage());

            return $this->_pageFactory->create();
        } catch (Exception $exception) {
            $this->_logger->log($this, 'error', __FUNCTION__ . ' error', [
                'response' => $exception->getMessage(),
                'code_exception' => $exception->getCode(),
                'file_exception' => $exception->getFile(),
                'line_exception' => $exception->getLine(),
            ]);

            return $this->_pageFactory->create();
        }
    }

    public function setPaymentApproved($payment, Transaction $transaction)
    {
        $payment->addTransactionCommentsToOrder($transaction, __('Payment approved'));
    }

    public function setPaymentDenied($payment, Transaction $transaction)
    {
        $payment->addTransactionCommentsToOrder($transaction, __('Payment declined'));
    }
}
