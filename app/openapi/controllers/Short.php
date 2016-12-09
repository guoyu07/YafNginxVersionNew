<?php

class Controller_Short extends Base_Controller {
      
    public $actions = array(
        'index'   => 'actions/short/Index.php',
        'shorten' => 'actions/short/Shorten.php',
        'expand'  => 'actions/short/Expand.php',
        'custom'  => 'actions/short/Custom.php', 
        'search'  => 'actions/short/Search.php',
    );
}
