<?php

class Action_Search extends Admin_Action_Page {

    public function execute () {
        parent::execute();
        
        $username = $this->_checkStatus();

        if ($username == false) {
            $url = '/index/login';
            header("Location:{$url}");
        }
        $this->setView('username', $username);
        $this->setView('tab', 2);
        $this->display('index/search.phtml');
        
    }
}
