<?php

/**
 * Page基础控制器，其他控制器会继承
 * @author : Levin Xu < levin@chope.co >
 *
 */

class Admin_Action_Page extends Base_Action {

    public function execute(){
        parent::execute();
    } 
     
    /**
     * 登陆状态监测
     * @return boolean false | string $username
     *
     */
    protected function _checkStatus(){
        $auth = Base_Cookie::get('auth');
        if (empty($auth)) {
            return false;
        }
        $username = Base_Common::authCode($auth, 'DECODE');
        return $username;
    }
}
