<?php

class Controller_Index extends Base_Controller {
      
    public $actions = array(
        "index"    => "actions/index/Index.php",
        "login"    => "actions/index/Login.php",
        "search"   => "actions/index/Search.php",
        "dologin"  => "actions/index/DoLogin.php",
    );

}
