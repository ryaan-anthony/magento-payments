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

class Radial_Payments_Model_Cron
{
    /**
     * @param Varien_Event_Observer
     */
    public function retrySettlements()
    {
        $this->retryInvoices();
        $this->retryCreditmemos();
    }

    /**
     * Retry settlement calls for all pending creditmemos
     */
    protected function retryCreditmemos()
    {
        $creditmemoCollection = $this->getPendingCreditmemoCollection();
        foreach ($creditmemoCollection as $creditmemo) {
            $order = $creditmemo->getOrder();
            $payment = $order->getPayment();
            $payment->getMethodInstance()->processCreditmemo($creditmemo, $payment);
            $this->saveSettlementDocument($creditmemo);
        }
    }

    /**
     * Retry settlement calls for all pending invoices
     */
    protected function retryInvoices()
    {
        $invoiceCollection = $this->getPendingInvoiceCollection();
        foreach ($invoiceCollection as $invoice) {
            $order = $invoice->getOrder();
            $payment = $order->getPayment();
            $payment->getMethodInstance()->processInvoice($invoice, $payment);
            $this->saveSettlementDocument($invoice);
        }
    }

    /**
     * @return Mage_Sales_Model_Resource_Order_Invoice_Collection
     */
    protected function getPendingInvoiceCollection()
    {
        /** @var Mage_Sales_Model_Resource_Order_Invoice_Collection $collection */
        $collection = Mage::getModel('sales/order_invoice')->getCollection();
        $collection->addFieldToFilter('state', Mage_Sales_Model_Order_Invoice::STATE_OPEN);
        return $collection;
    }

    /**
     * @return Mage_Sales_Model_Resource_Order_Creditmemo_Collection
     */
    protected function getPendingCreditmemoCollection()
    {
        /** @var Mage_Sales_Model_Resource_Order_Creditmemo_Collection $collection */
        $collection = Mage::getModel('sales/order_creditmemo')->getCollection();
        $collection->addFieldToFilter('state', Mage_Sales_Model_Order_Creditmemo::STATE_OPEN);
        return $collection;
    }

    /**
     * Save data for invoice/creditmemo and related order
     *
     * @param   Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo
     * @return  self
     */
    protected function saveSettlementDocument($document)
    {
        $document->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($document)
            ->addObject($document->getOrder())
            ->save();
        return $this;
    }
}
