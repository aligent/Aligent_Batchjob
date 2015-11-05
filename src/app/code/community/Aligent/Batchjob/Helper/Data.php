<?php

/**
 * General Batch Job related helpers
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Helper_Data extends Mage_Core_Helper_Abstract {


    /**
     * Similar to the inbuilt PHP empty() function, but with some differences in
     * handling of certain values:
     * * False is considered not empty
     * * 0 and 0.0 are not empty
     * * Empty arrays are empty
     * * null is ok
     *
     * @param $vValue mixed Value to test
     * @return bolean True if empty
     */
    public function isEmpty($vValue) {
        if (is_array($vValue)) {
            if (count($vValue) == 0) {
                return true;
            } else {
                return false;
            }
        }

        if ($vValue === 0 || $vValue === '0') {
            return false;
        }

        if ($vValue === null) {
            return false;
        }

        if ($vValue === false) {
            return false;
        }

        if (trim($vValue) == '') {
            return true;
        }

        return false;
    }

}