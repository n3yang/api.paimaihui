<?php
class Api_Bootstrap extends Zend_Application_Module_Bootstrap
{
	protected function _initRegisterErrorHandler()
	{
		$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
		    'module'     => 'api',
		    'controller' => 'error',
		    'action'     => 'error'
		)));
	}
	

}