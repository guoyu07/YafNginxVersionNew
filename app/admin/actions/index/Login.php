<?php

class Action_Login extends Admin_Action_Page {

    public function execute () {
        parent::execute();
        
        $this->display('index/login.phtml');       
    }
}
