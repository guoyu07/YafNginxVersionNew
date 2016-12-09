<?php

class Action_Index extends Openapi_Action_Page {

    public function execute () {
        parent::execute(); 
        
        $idString = trim($this->arrInput['url']); 
  
        $id = Base_Common::alphaId($idString, true);

        $shortUrlService = new Service_ShortUrl();
        $url = $shortUrlService->getLongUrlById($id); 
    
        if (empty($url)) {
            Base_Log::warning('short url not exist', Openapi_Conf_ErrorCode::ERROR_SYSTEM, $id); 
            return false;
        }   
    
        Header("HTTP/1.1 301 Moved Permanently");
        Header("Location: {$url}");
        exit(); 
    }
}
