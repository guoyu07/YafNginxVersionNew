<?php

/**
 * 日志记录基础类
 * @author: Levin<levin@chope.co>
 */

class Base_Log
{
    const LOG_LEVEL_FATAL = 0x01;
    const LOG_LEVEL_WARNING = 0x02;
    const LOG_LEVEL_NOTICE = 0x04;
    const LOG_LEVEL_TRACE = 0x08;
    const LOG_LEVEL_DEBUG = 0x10;
    public static $arrLogLevels = array (
            self::LOG_LEVEL_FATAL => 'FATAL', 
            self::LOG_LEVEL_WARNING => 'WARNING', 
            self::LOG_LEVEL_NOTICE => 'NOTICE', 
            self::LOG_LEVEL_TRACE => 'TRACE', 
            self::LOG_LEVEL_DEBUG => 'DEBUG' 
    );
    const DEFAULT_FORMAT = '%L: %t [%f:%N] errno[%E] logId[%l] uri[%U] params[%S] %M';
    const AUTO_ROTATE = 1;
    public static $current_log_level = null;
    public static $current_file = null;
    public static $current_line = null;
    private static $level = null;
    private static $path = null;
    private static $app = null;
    
    /**
     * debug日志
     *
     * @param $msg 日志描述信息            
     * @param $errno 错误号            
     * @param $method 方法名            
     * @param $param 参数数组            
     */
    public static function debug($msg, $errno = 0, $params = NULL, $depth = 0)
    {
        self::writeLog(self::LOG_LEVEL_DEBUG, $msg, $errno, $params, $depth + 1);
    }
    
    /**
     * warning日志
     *
     * @param $msg 日志描述信息            
     * @param $errno 错误号            
     * @param $method 方法名            
     * @param $param 参数数组            
     */
    public static function warning($msg, $errno = 0, $params = NULL, $depth = 0)
    {
        self::writeLog(self::LOG_LEVEL_WARNING, $msg, $errno, $params, $depth + 1);
    }
    
    /**
     * trace日志
     *
     * @param $msg 日志描述信息            
     * @param $errno 错误号            
     * @param $method 方法名            
     * @param $param 参数数组            
     */
    public static function trace($msg, $errno, $params = null, $depth = 0)
    {
        self::writeLog(self::LOG_LEVEL_TRACE, $msg, $errno, $params, $depth + 1);
    }
    
    /**
     * fatal日志
     *
     * @param $msg 日志描述信息            
     * @param $errno 错误号            
     * @param $method 方法名            
     * @param $param 参数数组            
     */
    public static function fatal($msg, $errno, $params = null, $depth = 0)
    {
        self::writeLog(self::LOG_LEVEL_FATAL, $msg, $errno, $params, $depth + 1);
    }
    
    /**
     * notice日志
     *
     * @param $msg 日志描述信息            
     * @param $errno 错误号            
     * @param $method 方法名            
     * @param $param 参数数组            
     */
    public static function notice($msg, $errno, $params = null, $depth = 0)
    {
        self::writeLog(self::LOG_LEVEL_NOTICE, $msg, $errno, $params, $depth + 1);
    }
    private static function writeLog($intLevel, $msg, $errno, $params = null, $depth = 0)
    {
        
        $config = Base_Config::getConf('log');
        self::$level = $config['level'];
        self::$path = $config['path'];
        self::$app = $config['app'];


        if ($intLevel > self::$level)
        {
            return;
        }
        self::$current_log_level = $intLevel;
   
        $logDir = self::$path . self::$app .'/' . date('Ym');
        //递归创建目录
        self::recursiveMkdir($logDir);        
        $strLogFile = $logDir . '/log';

        if (($intLevel & self::LOG_LEVEL_WARNING) || ($intLevel & self::LOG_LEVEL_FATAL))
        {
            $strLogFile .= '.wf';
        }
        if (self::AUTO_ROTATE)
        {
            $strLogFile .= '.' . date('Ymd');
        }
        $trace = debug_backtrace();
        $depth2 = $depth + 1;
        if ($depth >= count($trace))
        {
            $depth = count($trace) - 1;
            $depth2 = $depth;
        }
        
        self::$current_file = isset($trace[$depth]['file']) ? $trace[$depth]['file'] : "";
        self::$current_line = isset($trace[$depth]['line']) ? $trace[$depth]['line'] : "";
        
        // get the format
        $format = self::DEFAULT_FORMAT;
        
        $matches = array ();
        $regex = '/%(?:{([^}]*)})?(.)/';
        preg_match_all($regex, $format, $matches);
        $prelim = array ();
        $action = array ();
        $prelim_done = array ();
        $len = count($matches[0]);
        for ($i = 0; $i < $len; $i++)
        {
            $code = $matches[2][$i];
            $param = $matches[1][$i];
            switch ($code)
            {
                case 'L':
                    $action[] = self::$arrLogLevels[self::$current_log_level];
                    break;
                case 't':
                    $action[] = date('Y-m-d H:i:s');
                    break;
                case 'f':
                    $action[] = self::$current_file;
                    break;
                case 'N':
                    $action[] = self::$current_line;
                    break;
                case 'E':
                    $action[] = $errno;
                    break;
                case 'l':
                    $action[] = self::genLogID();
                    break;
                case 'U':
                    $action[] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
                    break;
                case 'S':
                    $action[] = json_encode($params);
                    break;
                case 'M':
                    $action[] = $msg;
                    break;
                case '%':
                    $action[] = "'%'";
                    break;
                case 'm':
                    $action[] = $method;
                    break;
                default:
                    $action[] = "''";
            }
        }
        $strformat = preg_split($regex, $format);
        $code = var_export($strformat[0], true);
        for ($i = 1; $i < count($strformat); $i++)
        {
            $code .= trim($action[$i - 1], "'") . trim(var_export($strformat[$i], true), "'");
        }
        
        $code .= PHP_EOL;
        $code = trim($code, "'");
        return file_put_contents($strLogFile, $code, FILE_APPEND);
    }
    
    // 生成logid
    public static function genLogID()
    {
        if (defined('LOG_ID'))
        {
            return LOG_ID;
        }
        if (isset($_REQUEST['logid']))
        {
            define('LOG_ID', intval($_REQUEST['logid']));
        }
        else
        {
            $arr = gettimeofday();
            $logId = ((($arr['sec'] * 100000 + $arr['usec'] / 10) & 0x7FFFFFFF) | 0x80000000);
            define('LOG_ID', $logId);
        }
        return LOG_ID;
    }
    
    /**
     * 递归创建目录
     *
     * @param string $pathname
     *            需要创建的目录路径
     * @param int $mode
     *            创建的目录属性，默认为755
     * @return void
     */
    public static function recursiveMkdir($pathname, $mode = 0755)
    {
        return is_dir($pathname) ? true : mkdir($pathname, $mode, true);
    }
}
