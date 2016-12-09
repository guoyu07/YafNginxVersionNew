<?php

/**
 *  In Bootstrap Class, all method with suffix _init will be called by Yaf,
 *  All these method receive a common parameter : Yaf_Dispatcher $dispatcher
 *  And the Calling order same as statement order. 
 *
 */

class Bootstrap extends Yaf_Bootstrap_Abstract{

    /** 
     * 注册自己的路由协议
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initRoute(Yaf_Dispatcher $dispatcher) {

        $router = $dispatcher->getRouter();
    }


    public function _initConfig() {
        $config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set("config", $config);
    }

    public function _initLoader($dispatcher) {

        //当前app的library下的目录
        Yaf_Loader::getInstance()->registerLocalNameSpace(array("Admin"));
    }

    public function _initView(Yaf_Dispatcher $dispatcher) {

        //禁止ap自动渲染模板
        $dispatcher->disableView();
    }

    public function _initDefaultName(Yaf_Dispatcher $dispatcher) {
        $dispatcher->setDefaultModule("Index")->setDefaultController("Index")->setDefaultAction("login");
    }
}
