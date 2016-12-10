<?php

class Action_Search extends Openapi_Action_Base {

    public function execute () {
        parent::execute();  

        if (!isset($this->arrInput['url']) || empty($this->arrInput['url'])) {
            Base_Log::warning('params error', Openapi_Conf_ErrorCode::ERROR_PARAMS, $this->arrInput);
            Base_Message::showError('params error', $this->arrInput);
        }
        $url = trim($this->arrInput['url']);
        
        $url = urldecode($url);

        $shortUrlService = new Service_ShortUrl();  
        $result = $shortUrlService->getInfoByUrl($url);
        if ($result) {
            Base_Message::showSucc('ok', $result); 
        } else {
            Base_Message::showError('error', $result);
        }
    }
}
