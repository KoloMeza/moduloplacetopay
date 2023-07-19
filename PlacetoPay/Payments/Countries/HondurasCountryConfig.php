<?php

namespace PlacetoPay\Payments\Countries;

use PlacetoPay\Payments\Model\Adminhtml\Source\Country;
use PlacetoPay\Payments\Model\Adminhtml\Source\Mode;

abstract class HondurasCountryConfig extends CountryConfig
{
    public static function resolve(string $countryCode): bool
    {
        return Country::HONDURAS === $countryCode;
    }

    public static function getEndpoints(): array
    {
        return array_merge(parent::getEndpoints(), [
            Mode::PRODUCTION => 'https://pagoenlinea.bancatlan.hn'
        ]);
    }
}
