<?php

namespace PlacetoPay\Payments\Model\Adminhtml\Source;

use PlacetoPay\Payments\Countries\BelizeCountryConfig;
use PlacetoPay\Payments\Countries\ChileCountryConfig;
use PlacetoPay\Payments\Countries\CountryConfig;
use PlacetoPay\Payments\Countries\EcuadorCountryConfig;
use PlacetoPay\Payments\Countries\HondurasCountryConfig;

class Country
{
    public const COLOMBIA = 'CO';
    public const COSTA_RICA = 'CR';
    public const ECUADOR = 'EC';
    public const CHILE = 'CL';
    public const PUERTO_RICO = 'PR';
    public const HONDURAS = 'HN';
    public const BELIZE = 'BZ';

    public const COUNTRIES_CONFIG = [
        EcuadorCountryConfig::class,
        ChileCountryConfig::class,
        HondurasCountryConfig::class,
        BelizeCountryConfig::class,
        CountryConfig::class
    ];

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::COLOMBIA,
                'label' => __('Colombia'),
            ],
            [
                'value' => self::COSTA_RICA,
                'label' => __('Costa Rica'),
            ],
            [
                'value' => self::ECUADOR,
                'label' => __('Ecuador'),
            ],
            [
                'value' => self::CHILE,
                'label' => __('Chile'),
            ],
            [
                'value' => self::PUERTO_RICO,
                'label' => __('Puerto Rico'),
            ],
            [
                'value' => self::HONDURAS,
                'label' => __('Honduras'),
            ],
            [
                'value' => self::BELIZE,
                'label' => __('Belize')
            ]
        ];
    }
}
