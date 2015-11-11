<?php

/**
 * Used to create a new CSV file and write a header row.  Use a <header> child
 * node to define the key/value pairs for the internal field name and name to
 * appear in the header row.  e.g.
 *
 * <header>
 *     <entity_id>Entity Id</entity_id>
 *     <created_at>Creation Date</created_at>
 * </header>
 * 
 * Use with batchjob/itemTask_writeCsv and batchjob/step_closeCsv.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Model_Step_InitCsvWriter extends Aligent_Batchjob_Model_Step_Abstract {

    /**
     * Called to initiate processing on a given step.
     *
     * @return False to stop further processing of subsequent steps.
     */
    function run() {
        $vLocalFileName = $this->getParentJob()->getFilename();
        $vLocalDirPath = dirname($vLocalFileName);

        $this->getLogger()->log("Opening CSV file... ".$vLocalFileName, Zend_Log::INFO);

        $oIo = new Varien_Io_File();
        $oIo->open(array('path' => $vLocalDirPath));
        $oIo->streamOpen($vLocalFileName, 'w');

        $aHeader = array();
        foreach ($this->_oStepConfig->header->children() as $vFieldCode => $oFieldName) {
            $aHeader[$vFieldCode] = (string) $oFieldName;
        }

        // Create new file with header row.
        $oFileWriter = Mage::getModel('batchjob/simplefilewriter')
            ->setStreamWriter($oIo)
            ->setHeader($aHeader)
            ->writeHeaderRow();

        $this->getParentJob()->setIoFile($oIo);
        $this->getParentJob()->setFileWriter($oFileWriter);

        return true;

    }

}