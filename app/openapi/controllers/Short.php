<?php

class ShortController extends Yaf_Controller_Abstract {
      
    public $actions = array(
        "index"     => "actions/short/Index.php",
        "shorten"   => "actions/short/Shorten.php"
    );
}
