<?php

/**
 * Admin proxy api  
 * @author : Levin Xu < levin@chope.co >
 *
 */

class Action_Api extends Admin_Action_Base {

    public function execute () {
        parent::execute();

        $method = $this->arrInput['method']; 
        $methods = Admin_Conf_Common::$APIPROXY_METHODS;  
        if (!array_key_exists($method, $methods)) {
            Base_Message::showError('method not exist', 500, array($method, $methods));
        }
       
        $uri = $methods[$method];

        $param = $this->arrInput;

        try {
            $apiclient = new Base_ApiProxy();
            $from = 'admin'; //source from is admin
            $result = $apiclient->call($uri, $param, $from);
            if (isset($result['status']['code']) && $result['status']['code'] == 0) {
                Base_Message::showSucc('ok', $result['result']);
            } else {
                Base_Message::showError($result['status']['msg'], $result['result'], $result['status']['code']); 
            } 
        } catch (Exception $e) {
            Base_Log::warning('call ugc api failed: ' . $e->getMessage(), $e->getCode(), array($uri, $param));
        }
    }
}
