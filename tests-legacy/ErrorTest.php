<?php

class ErrorTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'error';
    function testPlugin()
    {
        ob_start();
        print_r(PMVC\plug($this->_plug));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString($this->_plug,$output);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    function testError()
    {
      $this->expectException(PHPUnit_Framework_Error::class);
      try {
        $errStr='error';
        $err = PMVC\plug($this->_plug);
        $err->setErrorReporting('all');
        trigger_error($errStr);
        $Errors =& PMVC\getOption(PMVC\ERRORS);
        $this->assertEquals($errStr,$Errors[PMVC\APP_LAST_ERROR]);
      } catch (Exception $e) { 
        throw new PHPUnit_Framework_Error(
          $e->getMessage(),
          0,
        );
      }
    }

    /**
     * @expectedException InvalidArgumentException 
     */
    function testSetErrorReportingFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $errStr='error';
        $err = PMVC\plug($this->_plug);
        $err->setErrorReporting('e_fake');
    }
}
