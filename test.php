<?php
PMVC\Load::plug();
PMVC\setPlugInFolder('../');
class ErrorTest extends PHPUnit_Framework_TestCase
{
    function testError()
    {
        $errStr='error';
        $err = PMVC\plug('error');
        $err->setErrorReporting(E_ALL);
        trigger_error($errStr);
        $Errors =& PMVC\getOption(PMVC\ERRORS);
        $this->assertEquals($errStr,$Errors[PMVC\APP_LAST_ERROR]);
    }
}
