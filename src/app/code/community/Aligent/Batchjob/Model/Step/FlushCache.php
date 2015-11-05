<?php

/**
 * Flush Magento caches after a batch operation.
 *
 * A <caches> node can be used in the step's XML definition to control which
 * caches are flushed.  If omitted, all caches are flushed.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_FlushCache extends Aligent_Batchjob_Model_Step_Abstract {

    protected $_aCaches = [];

    public function __construct($aArgs) {
        parent::__construct($aArgs);

        if ($this->_oStepConfig->caches != '') {
            $aCaches = explode(',', $this->_oStepConfig->caches);
            $this->_aCaches = array_map('trim', $aCaches);
        }
    }


    public function run() {
        $aAllCacheTypes = Mage::app()->useCache();
        foreach ($aAllCacheTypes as $vType => $iOption) {
            if ($this->_canFlush($vType)) {
                $this->getLogger()->log("Flushing cache type $vType...", Zend_Log::INFO);
                Mage::app()->getCacheInstance()->cleanType($vType);
                $this->getLogger()->log("Flushed cache type $vType!", Zend_Log::INFO);
            }
        }

        // Allow subsequent steps to execute.
        return true;
    }


    /**
     * Uses the <caches> XML node to determine which caches should be flushed.
     * If no <caches> node is supplied, all caches are flushed.
     *
     * @param $vType string Name of the cache to consider flushing
     * @return bool True if cache can be flushed
     */
    protected function _canFlush($vType) {
        // Flush everything if the XML doesn't specify specific caches to flush.
        if (count($this->_aCaches) == 0) {
            return true;
        }

        // ... otherwise only flush it if it's on the list.
        return in_array($vType, $this->_aCaches);
    }
}