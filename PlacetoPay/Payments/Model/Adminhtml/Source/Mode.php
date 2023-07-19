<?php

namespace PlacetoPay\Payments\Model\Adminhtml\Source;

class Mode
{
    public const DEVELOPMENT = 'development';
    public const TEST = 'test';
    public const PRODUCTION = 'production';
    public const CUSTOM = 'custom';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::DEVELOPMENT,
                'label' => __('Development'),
            ],
            [
                'value' => self::TEST,
                'label' => __('Test'),
            ],
            [
                'value' => self::PRODUCTION,
                'label' => __('Production'),
            ],
            [
                'value' => self::CUSTOM,
                'label' => __('Custom'),
            ]
        ];
    }
}
