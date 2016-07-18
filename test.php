<?php
PMVC\Load::plug();
PMVC\addPlugInFolders(['../']);
class ErrorTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'error';
    function testPlugin()
    {
        ob_start();
        print_r(PMVC\plug($this->_plug));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains($this->_plug,$output);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    function testError()
    {
        $errStr='error';
        $err = PMVC\plug($this->_plug);
        $err->setErrorReporting('all');
        trigger_error($errStr);
        $Errors =& PMVC\getOption(PMVC\ERRORS);
        $this->assertEquals($errStr,$Errors[PMVC\APP_LAST_ERROR]);
    }

    /**
     * @expectedException InvalidArgumentException 
     */
    function testSetErrorReportingFail()
    {
        $errStr='error';
        $err = PMVC\plug($this->_plug);
        $err->setErrorReporting('e_fake');
    }
}
