<?php

class Application_Model_Controller_Exception extends Zend_Controller_Action_Exception
{

	/**
	 * 缺少必要参数
	 * @var 
	 */
	const EXCEPTION_PARAM_REQUIRED = 40001;
	
	public function __construct($msg = '', $code = 0, Exception $previous = null) {
		parent::__construct($msg, $code, $previous);
	}
	
	
	public function get() {
		return $_SERVER['REQUEST_URI'];
	}
	
	public function getMessageByCode($code = 0) {
		;
	}
}