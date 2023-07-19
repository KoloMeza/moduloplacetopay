<?php

namespace PlacetoPay\Payments\Constants;

abstract class PaymentStatus
{
    public const PENDING_PAYMENT = 'pending_payment';
    public const PENDING = 'pending';
    public const CANCELED = 'CANCELED';
    public const PROCESSING = 'processing';
    public const APPROVED = 'APPROVED';
    public const REJECTED = 'REJECTED';
    public const SUCCESSFUL = 'SUCCESSFUl';
    public const FAILED = 'FAILED';
    public const REFUNDED_PAYMENT = 'refunded_payment';
    public const REFUNDED = 'Refunded';
}
