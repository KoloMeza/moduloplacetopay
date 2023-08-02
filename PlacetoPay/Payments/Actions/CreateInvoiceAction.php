<?php

namespace PlacetoPay\Payments\Actions;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;

abstract class CreateInvoiceAction
{
    /**
     * @throws LocalizedException
     */
    public static function execute(Order $order): void
    {
        if (! $order->canInvoice()) {
            return;
        }

        $invoice = $order->prepareInvoice();
        $invoice->register()->capture();
        $order->addRelatedObject($invoice);
    }
}
