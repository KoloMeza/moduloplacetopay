<?php

namespace PlacetoPay\Payments\Actions;

use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;

abstract class SetOrderInfoSession
{
    public static function withQouteId(Session $session, Order $order, $quoteID = true)
    {
        $session->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId())
            ->setLastOrderStatus($order->getStatus());

        if ($quoteID) {
            $session->setLastQuoteId($order->getQuoteId());
        }
    }
}
