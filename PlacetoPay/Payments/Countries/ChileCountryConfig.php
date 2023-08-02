<?php

namespace PlacetoPay\Payments\Countries;

use PlacetoPay\Payments\Constants\Client;
use PlacetoPay\Payments\Constants\Country;
use PlacetoPay\Payments\Helper\ParseData;
use PlacetoPay\Payments\Model\Adminhtml\Source\Mode;

abstract class ChileCountryConfig extends CountryConfig
{
    public static function resolve(string $countryCode): bool
    {
        return Country::CHILE === $countryCode;
    }

    public static function getEndpoints(string $client): array
    {
        return array_merge(parent::getEndpoints($client), [
            Mode::TEST => ParseData::unmaskString('uggcf://purpxbhg.grfg.trgarg.py'),
            Mode::PRODUCTION => ParseData::unmaskString('uggcf://purpxbhg.trgarg.py'),
        ]);
    }

    public static function getClient(): array
    {
        return [
            [
                'value' => ParseData::unmaskString(Client::GNT),
                'label' => __(ParseData::unmaskString(Client::GNT)),
            ],
        ];
    }
}
