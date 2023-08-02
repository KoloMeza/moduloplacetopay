<?php

namespace PlacetoPay\Payments\Concerns;

use Dnetix\Redirection\Message\RedirectInformation;
use Magento\Sales\Model\Order;
use PlacetoPay\Payments\Actions\CreateInvoiceAction;
use PlacetoPay\Payments\Constants\PaymentStatus;
use PlacetoPay\Payments\Helper\OrderHelper;

trait IsSetStatusOrderTrait
{
    public function setStatus(RedirectInformation $information, Order $order, Order\Payment $payment = null): void
    {
        $this->logger->info('The status of the payment with requestId ' . $information->requestId() .
            ' for the order ' . $order->getRealOrderId() . ' is ', [$information->status()->status()]);
        $status = $information->status();

        $data = OrderHelper::getState($status->status());

        if ($data['state'] !== null) {
            if (!$payment) {
                $payment = $order->getPayment();
            }

            $info = $this->config->getInfoModel();
            $transactions = $information->payment();
            $info->updateStatus($payment, $status, $transactions);

            if ($status->isApproved()) {
                if ($information->lastApprovedTransaction()->refunded()) {
                    $payment->setIsTransactionDenied(true);
                    $payment->setSkipOrderProcessing(true);
                    $this->logger->warning('The order ' . $order->getRealOrderId() .
                        ' with status ' . $order->getStatus() . ' the order will go to state ' . PaymentStatus::REFUNDED);
                    $order->setState(PaymentStatus::REFUNDED)->setStatus(PaymentStatus::REFUNDED_PAYMENT)->save();
                } else {
                    $this->logger->info('The order ' . $order->getRealOrderId() .
                        ' with status ' . $order->getStatus() . ' pass to state ' . PaymentStatus::APPROVED);
                    $payment->setIsTransactionPending(false);
                    $payment->setIsTransactionApproved(true);
                    $payment->setSkipOrderProcessing(true);
                    CreateInvoiceAction::execute($order);
                    $order->setEmailSent(true);
                    $order->setState($data['state'])->setStatus($data['orderStatus'])->save();
                }
            } elseif ($status->isRejected()) {
                $payment->setIsTransactionDenied(true);
                $payment->setSkipOrderProcessing(true);
                $this->logger->info('The order ' . $order->getRealOrderId() .
                    ' with status ' . $order->getStatus() . ' the order will go to state ' . PaymentStatus::APPROVED);
                $order->cancel()->save();
            } else {
                $this->logger->info('Change the state of the order ' . $order->getRealOrderId() . ' to  ', ['state: ' => $data['state']]);
                $this->logger->info('Setting order status of the order' . $order->getRealOrderId() . ' to  ', ['Order status: ' => $data['orderStatus']]);
                $order->setState($data['state'])->setStatus($data['orderStatus'])->save();
            }
        }
    }

    public function changeStatusOrderFail(Order $order, Order\Payment $payment = null): void
    {
        if (!$payment) {
            $payment = $order->getPayment();
        }
        $this->logger->warning('The order ' . $order->getRealOrderId() . ' cant resolve, the status resolve to ' . PaymentStatus::CANCELED);
        $payment->setIsTransactionDenied(true);
        $payment->setSkipOrderProcessing(true);
        $order->cancel()->save();
    }
}
