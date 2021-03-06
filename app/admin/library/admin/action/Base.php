<?php

/**
 * API  基础控制器，其他控制器会继承
 * @author : Levin Xu < levin@chope.co >
 *
 */

class Admin_Action_Base extends Base_Action  {

   

    public function execute() {
        parent::execute();   

        $this->arrInput['from'] = isset($this->arrInput['from']) ? $this->arrInput['from'] : '';
        $this->arrInput['sign'] = isset($this->arrInput['sign']) ? $this->arrInput['sign'] : '';
       
        if (empty($this->arrInput['from']) || !isset($this->arrInput['from'])) {
            Base_Log::warning('Please Apply Authorization Token !', 500, $this->arrInput);
            Base_Message::showError('Please Apply Authorization Token !', $this->arrInput);
        } 

        if (!isset(Admin_Conf_Common::$CLIENT_TOKENS[$this->arrInput['from']])) {
            Base_Log::warning('Please Apply Authorization Token !', 500, $this->arrInput);
            Base_Message::showError('Please Apply Authorization Token !', $this->arrInput);
        }

        //sign校验
        $sysSign = Base_Common::getSign($this->arrInput, Admin_Conf_Common::$CLIENT_TOKENS[$this->arrInput['from']]); 
        if ($sysSign !== $this->arrInput['sign'] && $this->arrInput['from'] != Admin_Conf_Common::CLIENT_CHOPE) {
            $this->arrInput['syssign'] = $sysSign;
            Base_Log::warning('bad sign', 0, $this->arrInput);
            Base_Message::showError('bad sign error !', $this->arrInput);
        } 
    }
}
