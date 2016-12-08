<?php

class IndexAction extends Openapi_Action_Page {

    public function init() {
        parent::init();
    }
     
    public function execute () {
   
        $this->setView('content', $this->arrInput['name']);
        $this->display('index/index.phtml'); 
    }
}
