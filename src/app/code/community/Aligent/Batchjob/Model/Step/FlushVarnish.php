<?php

/**
 * Flush Varnish cache after a batch operation.
 *
 * A <stores> node can be used in the step's XML definition to control which
 * store caches are flushed by supplying a list of store codes.  If omitted, all
 * stores are flushed.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_FlushVarnish extends Aligent_Batchjob_Model_Step_Abstract {

    protected $_aStoreIds = array();

    public function __construct($aArgs) {
        parent::__construct($aArgs);

        if ($this->_oStepConfig->stores != '') {
            $aStoreCodes = array_map('trim', explode(',', $this->_oStepConfig->stores));
            $this->_aStoreIds = Mage::getModel('core/store')->getCollection()->addFieldToFilter('code', array('in' => $aStoreCodes))->getAllIds();
        }
    }


    public function run() {
        $oCacheControl = Mage::getModel('varnishcache/control');

        if (count($this->_aStoreIds) == 0) {
            $this->getLogger()->log("Flushing Varnish cache for all stores...", Zend_Log::INFO);
            $aStoreDomains = Mage::helper('varnishcache/cache')->getStoreDomainList();
            $oCacheControl->clean($aStoreDomains);
            $this->getLogger()->log("Finished flushing Varnish cache for all stores.", Zend_Log::INFO);
        } else {
            foreach ($this->_aStoreIds as $iStoreId) {
                $this->getLogger()->log("Flushing Varnish cache for store {$iStoreId}...", Zend_Log::INFO);
                $aStoreDomains = Mage::helper('varnishcache/cache')->getStoreDomainList($iStoreId);
                $oCacheControl->clean($aStoreDomains);
                $this->getLogger()->log("Finished flushing Varnish cache for store {$iStoreId}.", Zend_Log::INFO);
            }
        }

        // Allow subsequent steps to execute.
        return true;
    }

}