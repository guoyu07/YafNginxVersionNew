<?php

class Action_Expand extends Openapi_Action_Base {

    public function execute () {
        parent::execute();
        
        if (!isset($this->arrInput['url']) || empty($this->arrInput['url'])) {
            Base_Log::warning('params url can not be empty', Openapi_Conf_ErrorCode::ERROR_PARAMS, $this->arrInput['url']);
            Base_Message::showError('params url can not be empty', $this->arrInput['url']);
        }
 
        $url = trim($this->arrInput['url']); 
               
        $url = urldecode($url);
            
        $arr = explode('/',  trim($url, '/'));
        $keyword = trim($arr[3]);

        $id = Base_Common::alphaId($keyword, true);
       
        $shortUrlService = new Service_ShortUrl();  
        $urlLong = $shortUrlService->getLongUrlById($id); 
        
        $result = array(
            'id' => $id,
            'url_short' => $url,
            'url_long'  => $urlLong,
            'status'    => empty($urlLong) ? false : true,
        ); 
        if ($urlLong) {
            Base_Message::showSucc('ok', $result);        
        } else {
            Base_Message::showError('error', $result);
        }
    }
}
