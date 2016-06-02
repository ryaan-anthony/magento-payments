<?php
/**
 * Copyright (c) 2013-2014 eBay Enterprise, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright   Copyright (c) 2013-2014 eBay Enterprise, Inc. (http://www.ebayenterprise.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Radial_CreditCard_Model_Observer
{
    /**
     * handle payment settlement status event
     * request.
     * @param  Varien_Event_Observer $observer
     * @return self
     */
    public function handlePaymentSettlementsStatusEvent(Varien_Event_Observer $observer)
    {
        Mage::getModel(
            'radial_creditcard/events_settlementStatus', 
            ['payload' => $observer->getEvent()->getPayload()]
        )->process();
    }
}
