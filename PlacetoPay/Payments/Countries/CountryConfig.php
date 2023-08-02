<?php

namespace PlacetoPay\Payments\Countries;

use PlacetoPay\Payments\Constants\Client;
use PlacetoPay\Payments\Helper\ParseData;
use PlacetoPay\Payments\Model\Adminhtml\Source\Mode;

abstract class CountryConfig implements CountryConfigInterface
{
    public static function resolve(string $countryCode): bool
    {
        return true;
    }

    public static function getEndpoints(string $client): array
    {
        return [
            Mode::DEVELOPMENT => 'https://dev.placetopay.com/redirection',
            Mode::TEST => 'https://checkout-test.placetopay.com',
            Mode::PRODUCTION => 'https://checkout.placetopay.com',
        ];
    }

    public static function getClient(): array
    {
        return [
            [
                'value' => ParseData::unmaskString(Client::PTP),
                'label' => __(ParseData::unmaskString(Client::PTP)),
            ],
        ];
    }
}
