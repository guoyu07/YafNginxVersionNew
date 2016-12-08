<?php

class ErrorAction extends Openapi_Action_Page {

    public function execute ($exception) {
        parent::execute(); 
        
        Yaf_Dispatcher::getInstance()->disableView();  
        /* error occurs */  
        switch ($exception->getCode()) {   
            case YAF_ERR_NOTFOUND_MODULE:  
            case YAF_ERR_NOTFOUND_CONTROLLER:  
            case YAF_ERR_NOTFOUND_ACTION:  
            case YAF_ERR_NOTFOUND_VIEW:  
                Base_Log::warning($exception->getMessage(), 404, array());
                Base_Message::showError('Yaf Exception', array(), 404);
                break;  
            default :   
                Base_Log::warning($exception->getMessage(), 0, array());
                Base_Message::showError('Yaf Exception', array(), 500); 
                break;  
        }
               
    }
}
