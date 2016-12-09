<?php

class Base_Proxy {
    
    /**
     * 地址映射
     * @var type 
     */
    private static $_envMapping = array(
            'online' => 'http://q.ur7.cn',
            'dev' => 'http://q.ur7.cn',
    );
    
    /**
     * 接口地址
     * @var type 
     */
    private $_url = '';
    
    /**
     * 构造函数
     * @param type $env
     */
    public function __construct($env = 'online') {
        
        if (empty(self::$_envMapping[$env])) {
            throw new Exception('Bad openapi server name.');
        }
        $this->_url = self::$_envMapping[$env];
    }

    /**
     * 调用接口，接收返回
     * @param string $uri 接口资源名称，如 /v2/comment/add
     * @param array $param 接口调用相关参数
     * @param string $env 测试开发环境（dev）还是线上环境(online)
     *
     * @return mixed
     */
    public function call($uri, $param, $from) {

        $url = $this->_url . '/' . trim($uri, '/');
        
        unset($param['method']);
        unset($param['sign']);
        
        $param['from'] = $from; // update or set

        $sign = Base_Common::getSign($param, $this->_getToken($from)); 
        $param['sign'] = $sign;

        $result = Base_Http::post($url, $param);
        $result = json_decode($result, true);
        
        if (!$result || !is_array($result) || !isset($result['status']['code'])) {
            Base_Log::warning('curl failed', 500, $result);
        }
        
        return $result;
    }

    /**
     * 获取 token
     * @staticvar array $tokens
     * @param type $from
     * @return string
     */
    private function _getToken($from) {
       
        $tokens = Admin_Conf_Common::$CLIENT_TOKENS; 
        if (!isset($tokens[$from])) {
            Base_Log::warning('TOKEN NOT EXIST', 500, array($from, $tokens)); 
        }
        return $tokens[$from];
    }
}
