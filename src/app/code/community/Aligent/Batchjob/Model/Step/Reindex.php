<?php

/**
 * Force Magento's indexers to run.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_Reindex extends Aligent_Batchjob_Model_Step_Abstract {

    public function run() {
        $aIndexes = explode(',', $this->_oStepConfig->indexes);
        $aIndexes = array_map('trim', $aIndexes);

        $processes = Mage::getModel('index/indexer')->getProcessesCollectionByCodes($aIndexes);
        foreach($processes as $key => $process) {
            if ($process->getIndexer()->getVisibility() === false) {
                $this->getLogger()->log("Skipped {$process->getIndexerCode()} index because it is invisible.");
            } else {
                $this->getLogger()->log("Rebuilding {$process->getIndexerCode()} index...");
                $process->reindexEverything();
                Mage::dispatchEvent($process->getIndexerCode() . '_shell_reindex_after');
                $this->getLogger()->log("Finished rebuilding {$process->getIndexerCode()} index!");
            }
        }
    }

}