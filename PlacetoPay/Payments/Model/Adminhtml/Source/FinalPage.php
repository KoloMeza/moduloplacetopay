<?php

namespace PlacetoPay\Payments\Model\Adminhtml\Source;

class FinalPage
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => 'magento_default',
                'label' => __('Default'),
            ],
            [
                'value' => 'order_info',
                'label' => __('Order Information'),
            ],
        ];
    }
}
