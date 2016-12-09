<?php

class Action_DoLogin extends Admin_Action_Page {

    public function execute () {
        parent::execute();
       
        $username = trim($this->arrInput['username']);    
        $password = trim($this->arrInput['password']); 
  
        $serviceUserinfo = new Service_Userinfo();  
        $userpass = $serviceUserinfo->getUserinfoByUsername($username);
        //登陆成功
        if (!empty($userpass) && md5($password) == $userpass) {
            //设置cookie，并跳转
            Base_Cookie::set('auth', Base_Common::authCode($username));
            $url = '/index/index';
        } else {
            Base_Log::fatal('login failed', 500, array($this->arrInput, $userpass));
            $url = '/index/login';
        }
        
        header("Location:{$url}"); 
 
    }
}
