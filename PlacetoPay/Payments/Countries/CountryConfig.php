<?php

namespace PlacetoPay\Payments\Countries;

use PlacetoPay\Payments\Model\Adminhtml\Source\Mode;

abstract class CountryConfig implements CountryConfigInterface
{
    public static function resolve(string $countryCode): bool
    {
        return true;
    }

    public static function getEndpoints(): array
    {
        return [
            Mode::DEVELOPMENT => 'https://dev.placetopay.com/redirection',
            Mode::TEST => 'https://checkout-test.placetopay.com',
            Mode::PRODUCTION => 'https://checkout.placetopay.com',
        ];
    }
}
