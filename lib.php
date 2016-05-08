<?php
/**
 * Created by PhpStorm.
 * User: Дом
 * Date: 08.05.2016
 * Time: 15:22
 */



/**
 * a simple logger
 * @param type $message
 * @param type $num
 */
function toLog2($message='') {
    if (is_object($message) || is_array($message)) {
        $message = print_r($message, true);
    }
    else
    {
        $args = func_get_args();
        array_shift($args);
        if (!empty($args))
        {
            foreach ($args as &$arg)
                if (is_object($arg) || is_array($arg))
                    $arg = print_r($arg, true);

            $message = vsprintf($message, $args);
        }
    }

    $content = '[' . getTimeStamp() . '] ' . $message . PHP_EOL;
    if (defined('LOG') && LOG !== '') {
        file_put_contents(LOG, $content, FILE_APPEND);
    } else {
        echo $content;
    }
}



function tolog( $msg , $bin = false )
{
    $msg = print_r($msg, true);

    $script = 'test';

    $ip = '10.0.0.1';//getRemoteIP();

    $logfile = 'testlog.log';

    $fp = @fopen($logfile, "ab+");

    //chmod($logfile, 0666);

    if (!$fp) {

        return false;
    }

    if ($fp && is_resource( $fp )) {


        if (!$bin) {
            @fwrite( $fp, sprintf( "[%s][%s][%s] %s\n", date( 'D, d M Y H:i:s O', time() ), $script, $ip, $msg ) );

        } else {
            $msg = bin2hex($msg);

            @fwrite($fp, sprintf("[%s][%s][%s] binary bytes: %d \n", date('D, d M Y H:i:s O', time()), $script, $ip, (strlen( $msg ) / 2)));

            @fwrite($fp, $msg);

            @fwrite($fp, "\n");
        }
    }

    @fclose($fp);

    return true;
}