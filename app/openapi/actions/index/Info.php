<?php

class InfoAction extends Openapi_Action_Base {

    public function execute () {
        parent::execute(); 
    
        Base_Message::showSucc('ok', $this->arrInput);    
    }
}
