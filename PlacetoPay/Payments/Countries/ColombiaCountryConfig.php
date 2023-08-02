<?php

namespace PlacetoPay\Payments\Countries;

use PlacetoPay\Payments\Constants\Client;
use PlacetoPay\Payments\Constants\Country;
use PlacetoPay\Payments\Helper\ParseData;
use PlacetoPay\Payments\Model\Adminhtml\Source\Mode;

class ColombiaCountryConfig extends CountryConfig
{
    public static function resolve(string $countryCode): bool
    {
        return Country::COLOMBIA === $countryCode;
    }

    public static function getEndpoints(string $client): array
    {
        if ($client === ParseData::unmaskString(Client::GOU)) {
            return array_merge(parent::getEndpoints($client), [
                Mode::PRODUCTION => ParseData::unmaskString('uggcf://purpxbhg.tbhcntbf.pbz.pb'),
                Mode::TEST => ParseData::unmaskString('uggcf://purpxbhg.grfg.tbhcntbf.pbz.pb')
            ]);
        }
        return parent::getEndpoints($client);
    }

    public static function getClient(): array
    {
        return [
            [
                'value' => ParseData::unmaskString(Client::PTP),
                'label' => __(ParseData::unmaskString(Client::PTP)),
            ],
            [
                'value' => ParseData::unmaskString(Client::GOU),
                'label' => __(ParseData::unmaskString(Client::GOU)),
            ],
        ];
    }
}
