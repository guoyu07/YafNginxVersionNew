<?php

class Action_Custom extends Openapi_Action_Base {

    public function execute () {
        parent::execute();  
       
        if (!isset($this->arrInput['url']) || empty($this->arrInput['url']) || !isset($this->arrInput['keyword'])) {
            Base_Log::warning('params error', Openapi_Conf_ErrorCode::ERROR_PARAMS, $this->arrInput);
            Base_Message::showError('params error', $this->arrInput);
        }
        
        $url = trim($this->arrInput['url']);

        $url = urldecode($url);

        $keyword = trim($this->arrInput['keyword']);
        $customId = Base_Common::alphaId($keyword, true);
        
        if (!empty($keyword) && (strpos($keyword, 'a') === 0)) {
            Base_Log::warning('error', 500, array());
            Base_Message::showError('keyword should not begin with letter a', array(), 10002);
        }

        $shortUrlService = new Service_ShortUrl();  
        $result = $shortUrlService->custom($url, $customId); 
        if ($result === 10001) {
            Base_Log::warning('keyword exists, should be custom again', 500, array());
            Base_Message::showError('keyword exists, should be custom again', $result, 10001);
        } else if ($result) {
            Base_Message::showSucc('ok', $result); 
        } else {
            Base_Log::warning('error', 500, array());
            Base_Message::showError('error', $result);
        }
 
    }
}
