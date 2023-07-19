<?php

namespace PlacetoPay\Payments\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset as BaseField;
use Magento\Config\Model\Config;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;

/**
 * Class Fieldset.
 */
class Fieldset extends BaseField
{
    /**
     * @var Config
     */
    protected $_backendConfig;

    /**
     * Fieldset constructor.
     *
     * @param Context $context
     * @param Session $authSession
     * @param Js      $jsHelper
     * @param Config  $backendConfig
     * @param array   $data
     */
    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        Config $backendConfig,
        array $data = []
    ) {
        $this->_backendConfig = $backendConfig;
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    /**
     * Add custom css class.
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function _getFrontendClass($element)
    {
        return parent::_getFrontendClass($element) . ' with-button enabled';
    }

    /**
     * Return header title part of html for payment solution.
     *
     * @param AbstractElement $element
     *
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function _getHeaderTitleHtml($element)
    {
        $html = '<div class="config-heading" >';
        $groupConfig = $element->getGroup();
        $htmlId = $element->getHtmlId();

        $html .= '<div class="button-container"><button type="button" ' .
            ' class="button action-configure' .
            (empty($groupConfig['placetopay_ec_separate']) ? '' : ' placetopay-ec-separate') .
            '" id="' .
            $htmlId .
            '-head" onclick="placetopayToggleSolution.call(this, \'' .
            $htmlId .
            "', '" .
            $this->getUrl(
                'adminhtml/*/state'
            ) . '\'); return false;"><span class="state-closed">' . __(
                'Configure'
            ) . '</span><span class="state-opened">' . __(
                'Close'
            ) . '</span></button>';

        if (! empty($groupConfig['more_url'])) {
            $html .= '<a class="link-more" href="' . $groupConfig['more_url'] . '" target="_blank">' . __(
                'Learn More'
            ) . '</a>';
        }
        if (! empty($groupConfig['demo_url'])) {
            $html .= '<a class="link-demo" href="' . $groupConfig['demo_url'] . '" target="_blank">' . __(
                'View Demo'
            ) . '</a>';
        }

        $html .= '</div>';
        $html .= '<div class="' . __('heading') . '"><strong>' . $element->getLegend() . '</strong>';

        if ($element->getComment()) {
            $html .= '<span class="heading-intro">' . $element->getComment() . '</span>';
        }
        $html .= '<div class="config-alt"></div>';
        $html .= '</div></div>';

        return $html;
    }

    /**
     * Return header comment part of html for payment solution.
     *
     * @param AbstractElement $element
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _getHeaderCommentHtml($element)
    {
        return '';
    }

    /**
     * Get collapsed state on-load.
     *
     * @param AbstractElement $element
     *
     * @return false
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _isCollapseState($element)
    {
        return false;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getExtraJs($element)
    {
        $script = "require(['jquery', 'prototype'], function(jQuery){
            window.placetopayToggleSolution = function (id, url) {
                var doScroll = false;
                Fieldset.toggleCollapse(id, url);
                if ($(this).hasClassName(\"open\")) {
                    $$(\".with-button button.button\").each(function(anotherButton) {
                        if (anotherButton != this && $(anotherButton).hasClassName(\"open\")) {
                            $(anotherButton).click();
                            doScroll = true;
                        }
                    }.bind(this));
                }
                if (doScroll) {
                    var pos = Element.cumulativeOffset($(this));
                    window.scrollTo(pos[0], pos[1] - 45);
                }
            }
        });";

        return $this->_jsHelper->getScript($script);
    }
}
