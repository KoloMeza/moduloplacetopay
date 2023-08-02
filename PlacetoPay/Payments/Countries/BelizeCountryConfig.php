<?php

namespace PlacetoPay\Payments\Countries;

use PlacetoPay\Payments\Constants\Country;
use PlacetoPay\Payments\Model\Adminhtml\Source\Mode;

abstract class BelizeCountryConfig extends CountryConfig
{
    public static function resolve(string $countryCode): bool
    {
        return Country::BELIZE === $countryCode;
    }

    public static function getEndpoints(string $client): array
    {
        return array_merge(parent::getEndpoints($client), [
            Mode::PRODUCTION => 'https://abgateway.atlabank.com'
        ]);
    }
}
