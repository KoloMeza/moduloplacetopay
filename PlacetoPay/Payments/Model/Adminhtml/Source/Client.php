<?php

namespace PlacetoPay\Payments\Model\Adminhtml\Source;

use PlacetoPay\Payments\Countries\CountryConfigInterface;
use PlacetoPay\Payments\Constants\Country;
use PlacetoPay\Payments\Helper\Data;

class Client
{
    /**
     * @var Data
     */
    protected $dataHelper;

    public function __construct(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    public function toOptionArray(): array
    {
        $countryCode = $this->dataHelper->getCountryCode();

        /** @var CountryConfigInterface $config */
        foreach (Country::COUNTRIES_CLIENT as $config) {
            if (!$config::resolve($countryCode)) {
                continue;
            }
            return $config::getClient();
        }
        return [];
    }
}
