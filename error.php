<?php
namespace PMVC\PlugIn\error;

use InvalidArgumentException;
use PMVC as p;
use PMVC\Event;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\error';

class error extends p\PlugIn
{
    public $_error = [ 
        p\USER_ERRORS=>[
            E_USER_ERROR
        ],
        p\APP_ERRORS=>[
            E_USER_WARNING,
            E_USER_NOTICE,
            E_USER_DEPRECATED,
        ],
    ];
    
    public function init()
    {
        set_error_handler([$this,'handleError']);
        set_exception_handler([$this,'handleException']);
        register_shutdown_function(function () {
            error_reporting(0);
            \PMVC\dev(function(){
                $this->setErrorReporting('all');
                return [
                    'memory-use'=>memory_get_usage(),
                    'memory-set'=>ini_get('memory_limit'),
                    'isFinish'=>\PMVC\getOption(Event\FINISH)
                ];
            },'fatal');
        });
        if (isset($this[0])) {
            $this->setErrorReporting($this[0]);
        }
    }

    public function __destruct()
    {
        restore_error_handler();
        restore_exception_handler();
    }

    public function setErrorReporting($level)
    {
        ini_set('display_errors', true);
        ini_set('display_startup_errors', true);
        $func = 'e_'.$level;
        if ($this->isCallable($func)) {
            $int = $this->$func();
            error_reporting($int);
        } else {
            throw new InvalidArgumentException('Only accept [error|warning|notice|all]. You value is ['.$level.']');
        }
    }

    public function e_error()
    {
        return (int) E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR | E_PARSE;
    }

    public function e_warning()
    {
        return (int) $this->e_error() | E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING;
    }

    public function e_notice()
    {
        return (int) $this->e_warning() | E_NOTICE | E_USER_NOTICE | E_DEPRECATED | E_USER_DEPRECATED;
    }

    public function e_all()
    {
        return (int) $this->e_notice() | E_STRICT;
    }

    public function handleError($number, $message, $file = null, $line = null, $context = null)
    {
        $Errors = p\getOption(p\ERRORS); //HashMap
        if (in_array($number, $this->_error[p\USER_ERRORS])) {
            $ref =& \PMVC\ref($Errors->{p\USER_ERRORS});
            $ref[] = $message;
            $Errors[p\USER_LAST_ERROR]=$message;
        } elseif (in_array($number, $this->_error[p\APP_ERRORS])) {
            $ref =& \PMVC\ref($Errors->{p\APP_ERRORS});
            $ref[] = $message;
            $Errors[p\APP_LAST_ERROR]=$message;
            p\d($message);
        } else {
            $ref =& \PMVC\ref($Errors->{p\SYSTEM_ERRORS});
            $ref[] = $message;
            $Errors[p\SYSTEM_LAST_ERROR]=$message;
            p\d($message);
        }
    }

    public function handleException($exception)
    {
        $Errors = p\getOption(p\ERRORS); //HashMap
        $message = $exception->getMessage();
        $ref =& \PMVC\ref($Errors->{p\APP_ERRORS});
        $ref[] = $message;
        $Errors[p\APP_LAST_ERROR]=$message;
        p\d($exception);
    }
} //end class
