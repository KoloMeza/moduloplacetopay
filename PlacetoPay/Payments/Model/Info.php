<?php

namespace PlacetoPay\Payments\Model;

use Dnetix\Redirection\Entities\Status;
use Dnetix\Redirection\Entities\Transaction;
use Dnetix\Redirection\Message\RedirectResponse;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction as TransactionModel;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use PlacetoPay\Payments\Exception\PlacetoPayException;

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
        Order $order
    ) {
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

        $payment->setAdditionalInformation([
            'request_id' => $response->requestId(),
            'process_url' => $response->processUrl(),
            'status' => $response->status()->status(),
            'status_reason' => $response->status()->reason(),
            'status_message' => $response->status()->message(),
            'status_date' => $response->status()->date(),
            'environment' => $env,
            'transactions' => [],
        ]);

        try {
            $this->orderPaymentRepository->save($payment);
        } catch (Exception $ex) {
            throw new PlacetoPayException($ex->getMessage(), 401);
        }
    }

    /**
     * @throws LocalizedException
     * @throws PlacetoPayException
     */
    public function updateStatus(Payment $payment, Status $status, array $transactions)
    {
        $information = $payment->getAdditionalInformation();
        $parsedTransactions = $information['transactions'];
        $lastTransaction = null;

        if ($transactions) {
            /** @var Transaction $lastTransaction */
            $lastTransaction = $transactions[0];


            foreach ($transactions as $transaction) {

                //TODO refactorizar, tomar los datos de $processorFieldm
                $bin = '';
                $lastDigits = '';
                $installments = '';
                $batch = '';
                $line = '';

                foreach ($transaction->processorFields() as $processorField) {
                    if ($processorField->keyword() == "bin") $bin = $processorField->value();
                    if ($processorField->keyword() == "batch") $batch = $processorField->value();
                    if ($processorField->keyword() == "line") $line = $processorField->value();

                    if ($processorField->keyword() == "lastDigits") $lastDigits = $processorField->value();

                    if(is_array($processorField->value())){
                        if(isset($processorField->value()['installments'])){
                            $installments = $processorField->value()['installments'];

                        }
                    }

                    if ($bin != '' && $lastDigits != '') break;
                }
                //TODO fin de la refactorización
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
            'processor_field' => $lastTransaction ? $lastTransaction->processorFieldsToArray() : null
        ]);
    }

    /**
     * @throws LocalizedException
     * @throws PlacetoPayException
     */
    public function importToPayment(Payment $payment, array $data)
    {
        $actual = $payment->getAdditionalInformation() ? $payment->getAdditionalInformation() : [];

        $payment->setAdditionalInformation(array_merge($actual, $data));

        try {
            $this->orderPaymentRepository->save($payment);
        } catch (Exception $ex) {
            throw new PlacetoPayException($ex->getMessage(), 401);
        }
    }
}
