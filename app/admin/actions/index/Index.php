<?php

/**
 * Admin main info display page
 * @author : Levin Xu < levin@chope.co >
 *
 */

class Action_Index extends Admin_Action_Page {

    public function execute () {
        parent::execute();
        
        $username = $this->_checkStatus();

        if ($username == false) {
            $url = '/index/login';
            header("Location:{$url}");
        }
        $this->setView('username', $username);
        $this->setView('tab', 1);
        $this->display('index/index.phtml');
    }
}
