<?php

class Base_Action extends Yaf_Action_Abstract  {  


    public $arrInput = [];

    protected $view = array();

    public function execute(){
        $params = $this->getRequest()->getParams(); //route 配置的参数                                                
        $post = $this->getRequest()->getPost(); //全部的post参数                                                      
        $get = $this->getRequest()->getQuery(); //全部的get参数 
        $this->arrInput = array_merge($params, $post, $get);                                                          
        foreach ($this->arrInput  as $key => $value) { 
            $this->arrInput[$key] = htmlspecialchars(trim($this->arrInput[$key]), ENT_QUOTES);                        
        }
    }

    /**     
     *      
     * 设置模版变量                                                                                                   
     * @param string $key  模板变量名                                                                                 
     * @param mixed $value 模板变量值
     */     
    protected function setView($key, $value) {
        $this->getView()->assign($key, $value);
    }       
            
    /**     
     *  
     * 显示模版                                                                                                       
     * @param string $tplFile                                                                                         
     * @return 
     */     
    protected function display($tplFile, array $param = NULL) {                                                             
         $this->getView()->display($tplFile, $param);
    }  
}
