<?php
namespace PMVC\PlugIn\error;

use PMVC as p;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\error';

class error extends p\PlugIn
{
    public $_error = array(
        E_ERROR=>'E_ERROR',
        E_WARNING=>'E_WARNING',
        E_PARSE=>'E_PARSE',
        E_NOTICE=>'E_NOTICE',
        E_CORE_ERROR=>'E_CORE_ERROR',
        E_CORE_WARNING=>'E_CORE_WARNING',
        E_COMPILE_ERROR=>'E_COMPILE_ERROR',
        E_COMPILE_WARNING=>'E_COMPILE_WARNING',
        E_USER_ERROR=>'E_USER_ERROR',
        E_USER_WARNING=>'E_USER_WARNING',
        E_USER_NOTICE=>'E_USER_NOTICE',
        p\USER_ERRORS=>array(
            E_USER_ERROR=>'E_USER_ERROR'
        ),
        p\APP_ERRORS=>array(
            E_USER_WARNING=>'E_USER_WARNING',
            E_USER_NOTICE=>'E_USER_NOTICE',
        ),
    );
    
    public function init()
    {
        p\call_plugin(
            'dispatcher',
            'attach',
            array(
                $this,
                'SetConfig'
            )
        );
        p\call_plugin(
            'dispatcher',
            'attach',
            array(
                $this,
                'Finish'
            )
        );
        set_error_handler(array($this,'handleError'));
        set_exception_handler(array($this,'handleException'));
    }

    public function onSetConfig()
    {
        if (p\plug('dispatcher')->isSetOption(_ERROR_REPORTING)) {
            $this->setErrorReporting(p\getOption(_ERROR_REPORTING));
        }
    }

    public function onFinish()
    {
        restore_error_handler();
    }

    public function setErrorReporting($level)
    {
        error_reporting($level);
        ini_set('display_errors', true);
        ini_set('display_startup_errors', true);
    }

    public function handleError($number, $message, $file, $line, $context)
    {
        if (!isset($this->_error[$number])) {
            return null;
        }
        $Errors =& p\getOption(p\ERRORS);
        if (isset($this->_error[p\USER_ERRORS][$number])) {
            $Errors[p\USER_ERRORS][]=$message;
            $Errors[p\USER_LAST_ERROR]=$message;
        } elseif (isset($this->_error[p\APP_ERRORS][$number])) {
            $Errors[p\APP_ERRORS][]=$message;
            $Errors[p\APP_LAST_ERROR]=$message;
            p\d($message);
        } else {
            $Errors[p\SYSTEM_ERRORS][]=$message;
            $Errors[p\SYSTEM_LAST_ERROR]=$message;
            p\d($message);
        }
    }

    public function handleException($exception)
    {
        p\d($exception);
        $Errors =& p\getOption(p\ERRORS);
        $message = $exception->getMessage();
        $Errors[p\APP_ERRORS][]=$message;
        $Errors[p\APP_LAST_ERROR]=$message;
    }
} //end class
