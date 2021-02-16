<?php
/**
 * Observer
 *
 * This file is responsible for execute functions from observers
 *
 * @category   Cartreset
 * @package    Fix bugs
 * @author     Cammino Digital <contato@cammino.com.br>
 */

// Observer
class Cammino_Cartreset_Model_Observer extends Mage_Checkout_Block_Onepage_Abstract {
    
    // Observer
	public function loadCustomerQuote() {
        if (Mage::getStoreConfig("themeconfig/themeconfig_cart_reset/active")) {
            return true;
        }
        $customerQuote = Mage::getModel('sales/quote') 
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId() ); 

        if ( $customerQuote->getId() && $this->getQuoteId() != $customerQuote->getId() ) { 

            // Removing old cart items of the customer. 
            foreach ($customerQuote->getAllItems() as $item) { 
                $item->isDeleted(true); 

                if ( $item->getHasChildren() ) { 

                    foreach ($item->getChildren() as $child) { 
                        $child->isDeleted(true); 
                    } 
                } 
            } 
            
            $customerQuote->collectTotals()->save(); 
        } 
        else { 
            $this->getQuote()->getBillingAddress(); 
            $this->getQuote()->getShippingAddress(); 
            $this->getQuote()->setCustomer(Mage::getSingleton('customer/session')->getCustomer())->setTotalsCollectedFlag(false)->collectTotals()->save();
        } 

        return $this;
	}
}
