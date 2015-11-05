<?php

/**
 * Translation helper tests
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case {


    /**
     * Tests the custom implementation of empty()
     *
     * @test
     * @dataProvider dataProvider
     * @param $vValue mixed Value to test
     * @param $bExpectedToBeEmpty boolean Whether or not it should be considered empty.
     */
    public function testIsEmpty($vValue, $bExpectedToBeEmpty) {
        $bEmpty = Mage::helper('batchjob')->isEmpty($vValue);
        $this->assertEquals($bExpectedToBeEmpty, $bEmpty);
    }


}