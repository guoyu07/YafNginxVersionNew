<?php

class IndexAction extends Openapi_Action_Page {

    public function execute () {
        parent::execute(); 
        
        $this->setView('content', "Hello");
        $this->display('index/index.phtml'); 
    }
}
