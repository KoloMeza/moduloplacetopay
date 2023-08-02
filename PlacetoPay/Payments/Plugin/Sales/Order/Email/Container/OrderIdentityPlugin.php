<?php

namespace PlacetoPay\Payments\Plugin\Sales\Order\Email\Container;

use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use PlacetoPay\Payments\Helper\Data;

/**
 * Class OrderIdentityPlugin.
 */
class OrderIdentityPlugin
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Data
     */
    protected $config;

    public function __construct(
        Session $session,
        Data $config
    ) {
        $this->session = $session;
        $this->config = $config;
    }

    public function aroundIsEnabled(OrderIdentity $subject, callable $proceed): bool
    {
        $canProceed = $proceed();
        $emailSuccess = $this->config->getEmailSuccessOption();
        $canSendEmail = $this->session->getLastRealOrder()->getCanSendNewEmailFlag();

        if ($emailSuccess) {
            if (isset($canSendEmail) && $canSendEmail) {
                if ($canProceed) {
                    $canProceed = false;
                } else {
                    $canProceed = true;
                }
            }
        }

        return $canProceed;
    }
}
