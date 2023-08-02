<?php

namespace PlacetoPay\Payments\Countries;

use PlacetoPay\Payments\Constants\Country;
use PlacetoPay\Payments\Model\Adminhtml\Source\Mode;

abstract class UruguayCountryConfig extends CountryConfig
{
    public static function resolve(string $countryCode): bool
    {
        return Country::URUGUAY === $countryCode;
    }

    public static function getEndpoints(string $client): array
    {
        return array_merge(parent::getEndpoints($client), [
            Mode::TEST => 'https://uy-uat-checkout.placetopay.com',
            Mode::PRODUCTION => 'https://checkout.placetopay.uy',
        ]);
    }
}
