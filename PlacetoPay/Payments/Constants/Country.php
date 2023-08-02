<?php

namespace PlacetoPay\Payments\Constants;

use PlacetoPay\Payments\Countries\BelizeCountryConfig;
use PlacetoPay\Payments\Countries\ChileCountryConfig;
use PlacetoPay\Payments\Countries\ColombiaCountryConfig;
use PlacetoPay\Payments\Countries\CountryConfig;
use PlacetoPay\Payments\Countries\EcuadorCountryConfig;
use PlacetoPay\Payments\Countries\HondurasCountryConfig;
use PlacetoPay\Payments\Countries\UruguayCountryConfig;

class Country
{
    public const COLOMBIA = 'CO';
    public const COSTA_RICA = 'CR';
    public const ECUADOR = 'EC';
    public const CHILE = 'CL';
    public const PUERTO_RICO = 'PR';
    public const HONDURAS = 'HN';
    public const BELIZE = 'BZ';

    public const PANAMA = 'PA';

    public const URUGUAY = 'UY';

    public const COUNTRIES_CONFIG = [
        EcuadorCountryConfig::class,
        ChileCountryConfig::class,
        HondurasCountryConfig::class,
        BelizeCountryConfig::class,
        UruguayCountryConfig::class,
        CountryConfig::class
    ];

    public const COUNTRIES_CLIENT = [
        ColombiaCountryConfig::class,
        ChileCountryConfig::class,
        CountryConfig::class
    ];
}
