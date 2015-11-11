<?php


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