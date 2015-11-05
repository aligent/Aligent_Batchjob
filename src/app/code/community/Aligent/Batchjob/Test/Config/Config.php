<?php

/**
 * Simple smoke test for module.
 *
 * @author Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_Batchjob_Test_Config_Config extends EcomDev_PHPUnit_Test_Case_Config {

    /**
     * Smoke test, assert module enabled and class aliases resolve as expected.
     */
    public function testModuleSmoke() {
        $this->assertModuleIsActive();
        $this->assertHelperAlias('batchjob', 'Aligent_Batchjob_Helper_Data');
        $this->assertModelAlias('batchjob/job_factory', 'Aligent_Batchjob_Model_Job_Factory');
    }
}
