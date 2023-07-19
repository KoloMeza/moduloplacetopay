<?php

namespace PlacetoPay\Payments\Controller\Onepage;

use Magento\Checkout\Controller\Onepage;
use Magento\Checkout\Model\Session\SuccessValidator;

/**
 * Class Success.
 */
class Success extends Onepage
{
    /**
     * @return \Magento\Framework\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $session = $this->getOnepage()->getCheckout();

        if (!$this->_objectManager->get(SuccessValidator::class)->isValid()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $session->clearQuote();

        $resultPage = $this->resultPageFactory->create();

        $this->_eventManager->dispatch(
            'checkout_onepage_controller_success_action',
            [
                'order_ids' => [$session->getLastOrderId()],
                'order' => $session->getLastRealOrder()
            ]
        );

        return $resultPage;
    }
}
