<?php

namespace PlacetoPay\Payments\Model;

use Dnetix\Redirection\Entities\Status;
use Dnetix\Redirection\Message\RedirectResponse;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction as TransactionModel;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use PlacetoPay\Payments\Exception\PlacetoPayException;
use PlacetoPay\Payments\Helper\DebugLogger;
use Psr\Log\LoggerInterface;

class Info
{
    /**
     * @var BuilderInterface
     */
    protected $transactionBuilder;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    protected $orderPaymentRepository;

    public function __construct(BuilderInterface $transactionBuilder, OrderPaymentRepositoryInterface $orderPaymentRepository)
    {
        $this->transactionBuilder = $transactionBuilder;
        $this->orderPaymentRepository = $orderPaymentRepository;
    }

    /**
     * @throws LocalizedException
     * @throws PlacetoPayException
     */
    public function loadInformationFromRedirectResponse(
        Payment $payment,
        RedirectResponse $response,
        string $env,
        Order $order,
        DebugLogger $logger
    ) {
        $logger->logInfo('Add information to payment ' , ['id' => $payment->getId()]);

        $logger->logInfo('Response information ' , ['data' => $response->toArray()]);

        $payment->setLastTransId($response->requestId());
        $payment->setTransactionId($response->requestId());
        $payment->setIsTransactionClosed(0);
        $payment->setParentTransactionId($order->getId());
        $payment->setIsTransactionPending(true);

        /** @var TransactionModel $transaction */
        $transaction = $this->transactionBuilder->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($payment->getTransactionId())
            ->build(TransactionModel::TYPE_ORDER);

        $payment->addTransactionCommentsToOrder($transaction, __('pending'));

        $logger->logInfo('Transaction information ' , ['data' => $transaction->toArray()]);

        $additionalInformation = [
            'request_id' => $response->requestId(),
            'process_url' => $response->processUrl(),
            'status' => $response->status()->status(),
            'status_reason' => $response->status()->reason(),
            'status_message' => $response->status()->message(),
            'status_date' => $response->status()->date(),
            'environment' => $env,
            'transactions' => [],
        ];

        $logger->logInfo('Additional information', ['data' => $additionalInformation]);

        $payment->setAdditionalInformation($additionalInformation);

        try {
            $this->orderPaymentRepository->save($payment);
            $logger->logWarning('Payment additional information', ['data' => $payment->getAdditionalInformation()]);
        } catch (Exception $ex) {
            $logger->logWarning('Exception to add additional information', ['exception' => $ex->getMessage()]);
            throw new PlacetoPayException($ex->getMessage(), 401);
        }
    }

    /**
     * @throws LocalizedException
     * @throws PlacetoPayException
     */
    public function updateStatus(Payment $payment, Status $status,DebugLogger $logger, ?array $transactions = null)
    {
        $information = $payment->getAdditionalInformation();
        $parsedTransactions = $information['transactions'];
        $lastTransaction = null;

        if ($transactions && is_array($transactions) && !empty($transactions)) {
            $lastTransaction = $transactions[0];


            foreach ($transactions as $transaction) {

                $bin = '';
                $lastDigits = '';
                $installments = '';
                $batch = '';
                $line = '';
                $logger->logInfo('DEBUG LOTE');
                $logger->logInfo(print_r($transaction->processorFields(),true));
                //TODO traer cuotas de pago

                foreach ($transaction->processorFields() as $processorField) {
                    if ($processorField->keyword() == "bin") $bin = $processorField->value();
                    if ($processorField->keyword() == "batch") $batch = $processorField->value();
                    if ($processorField->keyword() == "line") $line = $processorField->value();

                    if ($processorField->keyword() == "lastDigits") $lastDigits = $processorField->value();

                    if(is_array($processorField->value())){
                        if(isset($processorField->value()['installments'])){
                            $installments = $processorField->value()['installments'];
                            $logger->info(print_r($installments,true));
                        }
                    }

                    if ($bin != '' && $lastDigits != '') break;
                }

                $logger->logInfo('DEBUG LOTE');


                $parsedTransactions[$transaction->internalReference()] = [
                    'authorization' => $transaction->authorization(),
                    'status' => $transaction->status()->status(),
                    'status_date' => $transaction->status()->date(),
                    'status_message' => $transaction->status()->message(),
                    'status_reason' => $transaction->status()->reason(),
                    'franchise' => $transaction->franchise(),
                    'payment_method_name' => $transaction->paymentMethodName(),
                    'payment_method' => $transaction->paymentMethod(),
                    'amount' => $transaction->amount()->from()->total(),
                    'bin' => $bin,
                    'lastDigits' => $lastDigits,
                    'issuerName' => $transaction->issuerName(),
                    'installments' => $installments,
                    'lote' => $batch,
                    'line' => $line,
                ];
            }
        }

        $this->importToPayment($payment, [
            'status' => $status->status(),
            'status_reason' => $status->reason(),
            'status_message' => $status->message(),
            'status_date' => $status->date(),
            'authorization' => $lastTransaction ? $lastTransaction->authorization() : null,
            'refunded' => $lastTransaction ? $lastTransaction->refunded() : false,
            'transactions' => $parsedTransactions,
        ], $logger);
    }

    /**
     * @throws LocalizedException
     * @throws PlacetoPayException
     */
    public function importToPayment(Payment $payment, array $data, DebugLogger $logger)
    {
        $actual = $payment->getAdditionalInformation() ? $payment->getAdditionalInformation() : [];

        $logger->logInfo('Initialize update additional information to payment', ['id' => $payment->getId()]);
        $logger->logInfo('Current information', ['data' => $actual]);
        $logger->logInfo('New data to update', ['data' => $data]);

        $payment->setAdditionalInformation(array_merge($actual, $data));
        $logger->logWarning('additional information', ['data' => $payment->getAdditionalInformation()]);

        try {
            $this->orderPaymentRepository->save($payment);
            $logger->logInfo('Payment information save', ['data' => $payment->getAdditionalInformation()]);
        } catch (Exception $ex) {
            $logger->logWarning('exception message', ['data' => $ex->getMessage()]);
            throw new PlacetoPayException($ex->getMessage(), 401);
        }
    }
}
