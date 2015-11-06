<?php

class Aligent_Batchjob_Model_Simplefilewriter extends Varien_Object {
    
    const DELIMITER = "\t";
    const ENCLOSURE = '"';

    public function writeHeaderRow() {
        $this->getStreamWriter()->streamWriteCsv(array_values($this->getHeader()), self::DELIMITER, self::ENCLOSURE);
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
        $this->getStreamWriter()->streamWriteCsv($aRow, self::DELIMITER, self::ENCLOSURE);
    }
}