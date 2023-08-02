<?php

namespace PlacetoPay\Payments\Block\Frontend\Onepage;

use Magento\Checkout\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;

/**
 * Class Pending.
 */
class Pending extends Template
{
    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var TimezoneInterface
     */
    protected $_timezone;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * Pending constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderFactory $orderFactory
     * @param TimezoneInterface $timezone
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        TimezoneInterface $timezone,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_timezone = $timezone;
        $this->_priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return mixed
     */
    public function getRealOrderId()
    {
        return $this->_checkoutSession->getLastRealOrderId();
    }

    /**
     *  Payment custom error message
     *
     * @return string
     */
    public function getWarningMessage()
    {
        return $this->_checkoutSession->getWarningMessage();
    }

    /**
     * Continue shopping URL
     *
     * @return string
     */
    public function getContinueShoppingUrl()
    {
        return $this->getUrl('checkout/cart');
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getOrder()
    {
        return $this->_orderFactory->create()->loadByIncrementId($this->getRealOrderId());
    }

    /**
     * @param $date
     * @param string $format
     * @return string
     */
    public function dateFormat($date, $format = 'd F Y')
    {
        return $this->_timezone->date($date)->format($format);
    }

    /**
     * @param $amount
     * @return string
     */
    public function getFormattedPrice($amount)
    {
        return $this->_priceCurrency->convertAndFormat($amount);
    }
}
