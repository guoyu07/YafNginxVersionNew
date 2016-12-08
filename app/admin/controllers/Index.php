<?php

class IndexController extends Yaf_Controller_Abstract {
      
    public $actions = array(
        "index" => "actions/index/Index.php",
        "user"  => "actions/index/User.php"
    );
}
