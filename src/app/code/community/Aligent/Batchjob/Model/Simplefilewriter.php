<?php

class Aligent_Batchjob_Model_Simplefilewriter extends Varien_Object {

    public function __construct() {
        parent::__construct();

        $this->setDelimiter(',')
            ->setEnclosure('"');
    }


    public function writeHeaderRow() {
        $this->getStreamWriter()->streamWriteCsv(array_values($this->getHeader()), $this->getDelimiter(), $this->getEnclosure());
        return $this;
    }
    
    public function writeDataRow($aData) {
        $aRow = array();
        foreach ($this->getHeader() as $vKey => $vTitle) {
            if (array_key_exists($vKey, $aData)) {
                $aRow[] = $aData[$vKey];
            } else {
                $aRow[] = '';
            }
        }
        $this->getStreamWriter()->streamWriteCsv($aRow, $this->getDelimiter(), $this->getEnclosure());
        return $this;
    }
}