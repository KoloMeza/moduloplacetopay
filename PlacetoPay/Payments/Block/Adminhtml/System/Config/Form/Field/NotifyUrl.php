<?php

namespace PlacetoPay\Payments\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\UrlInterface;

/**
 * Class NotifyUrl.
 */
class NotifyUrl extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $id = $element->_getData('scope_id');

        $baseUrl = $this->_storeManager->getStore($id)
            ->getBaseUrl(UrlInterface::URL_TYPE_WEB, true);

        return "{$baseUrl}rest/V1/placetopay/payment/notify";
    }
}
