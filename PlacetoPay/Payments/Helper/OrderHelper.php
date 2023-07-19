<?php

namespace PlacetoPay\Payments\Helper;

use Dnetix\Redirection\Entities\Status;
use Dnetix\Redirection\Message\RedirectInformation;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order;
use PlacetoPay\Payments\Constants\PaymentStatus;

abstract class OrderHelper
{
    public static function getState($status): array
    {
        switch ($status) {
            case Status::ST_APPROVED:
                $state = Order::STATE_PROCESSING;
                $orderStatus = Order::STATE_PROCESSING;
                break;
            case Status::ST_REJECTED:
                $state = Order::STATE_CANCELED;
                $orderStatus = Order::STATE_CANCELED;
                break;
            case Status::ST_PENDING:
                $state = Order::STATE_NEW;
                $orderStatus = Order::STATE_PENDING_PAYMENT;
                break;
            default:
                $state = null;
                $orderStatus = null;
        }

        return self::setState($state, $orderStatus);
    }

    protected static function setState(string $state, string $orderStatus): array
    {
        return [
            'state' => $state,
            'orderStatus' => $orderStatus
        ];
    }

    public static function parseOrderState(Order $order): Status
    {
        switch ($order->getStatus()) {
            case Order::STATE_COMPLETE:
                $status = Order::STATE_COMPLETE;
                break;
            case PaymentStatus::REFUNDED_PAYMENT:
                $status = Status::ST_REFUNDED;
                break;
            case Order::STATE_PROCESSING:
                $status = Status::ST_APPROVED;
                break;
            case Order::STATE_CANCELED:
                $status = Status::ST_REJECTED;
                break;
            case Order::STATE_NEW:
                $status = Status::ST_PENDING;
                break;
            default:
                $status = Status::ST_PENDING;
        }

        return new Status([
            'status' => $status,
        ]);
    }

    /**
     * @param OrderAddressInterface $address
     *
     * @return array
     */

    public static function parseAddressPerson($address): array
    {
        if ($address) {
            return [
                'name' => $address->getFirstname(),
                'surname' => $address->getLastname(),
                'email' => $address->getEmail(),
                'mobile' => $address->getTelephone(),
                'address' => [
                    'country' => $address->getCountryId(),
                    'state' => $address->getRegion(),
                    'city' => $address->getCity(),
                    'street' => implode(' ', $address->getStreet()),
                    //'phone' => $address->getTelephone(),
                    'postalCode' => $address->getPostcode(),
                ],
            ];
        }

        return [];
    }

    public static function isPendingOrder(Order $order): bool
    {
        return in_array($order->getStatus(), ['pending', 'pending_payment'], true);
    }

    public static function getPaymentStatus(RedirectInformation $information): Status
    {
        $status = $information->status();

        if ($information->isApproved() && $information->lastApprovedTransaction()->refunded()) {
            return new Status([
                'status' => Status::ST_REFUNDED,
            ]);
        }

        return $status;
    }
}
