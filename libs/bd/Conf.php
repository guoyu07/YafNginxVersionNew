<?php

/**
 * @file Conf.php
 * @author Levin <levin@chope.co>
 * @date 2016/09/10 12:42:33
 *  
 */

class Bd_Conf 
{
    protected static $set_conf = null;
    
    /* 
     * 获取配置
     * @param string $item: 指定配置项，表示获取全部配置
     *
     */
    public static function getConf($conf) {
       $arr = explode('/', $conf);
        if (count($arr) == 1) {
            $section = $conf;
            $filename = "{$conf}.ini";
        } else {
            $section = array_pop($arr);
            $filename = implode('/', $arr) . '.ini'; 
        }
        $config = new Yaf_Config_Ini(ROOT_PATH . '/conf/' . $filename, $section);
        return $config->toArray();
 

    }
}
