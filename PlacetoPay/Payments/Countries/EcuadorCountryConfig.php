<?php

namespace PlacetoPay\Payments\Countries;

use PlacetoPay\Payments\Constants\Country;
use PlacetoPay\Payments\Model\Adminhtml\Source\Mode;

abstract class EcuadorCountryConfig implements CountryConfigInterface
{
    public static function resolve(string $countryCode): bool
    {
        return Country::ECUADOR === $countryCode;
    }

    public static function getEndpoints(string $client): array
    {
        return [
            Mode::DEVELOPMENT => 'https://dev.placetopay.ec/redirection',
            Mode::TEST => 'https://checkout-test.placetopay.ec',
            Mode::PRODUCTION => 'https://checkout.placetopay.ec',
        ];
    }
}
