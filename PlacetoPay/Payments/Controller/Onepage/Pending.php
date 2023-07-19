<?php

namespace PlacetoPay\Payments\Controller\Onepage;

use Magento\Checkout\Controller\Onepage;

/**
 * Class Pending.
 */
class Pending extends Onepage
{
    /**
     * @return \Magento\Framework\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $session = $this->getOnepage()->getCheckout();

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();

        if (!$lastQuoteId || !$lastOrderId) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $session->clearQuote();

        return $this->resultPageFactory->create();
    }
}
