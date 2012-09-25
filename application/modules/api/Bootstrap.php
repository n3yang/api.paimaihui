<?php
class Api_Bootstrap extends Zend_Application_Module_Bootstrap
{
	/**
	 * 注册错误处理插件，使此Module的错误处理定向到apiModule/errorController/errorAction
	 * 
	 */
	protected function _initPluginErrorHandler()
	{
		$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
		    'module'     => 'api',
		    'controller' => 'error',
		    'action'     => 'error'
		)));
	}
	
}