<?php

class Application_Model_Controller_Exception extends Zend_Controller_Action_Exception
{

	const E_UNKNOW = 40000;
	
	/**
	 * 缺少必要参数 
	 */
	const E_PARAM_REQUIRED = 40001;
	/**
	 * 重复的数据
	 */
	const E_PARAM_DUPLICATE_KEY = 40002;
	/**
	 * 数据更新错误
	 */
	const E_DB_UPDATE = 40003;
	
	
	public function __construct($msg = '', $code = 0, Exception $previous = null) 
	{
		parent::__construct($msg, $code, $previous);
	}
	
	public function getReturn() 
	{
		$rtn = array(
			'error'			=> empty($this->message) ? $this->getMessageByCode($this->code) : $this->message,
			'error_code'	=> $this->code,
			'request'		=> $_SERVER['REQUEST_URI'],
		);
		return json_encode($rtn);
	}
	
	public function getMessageByCode($code = 0) 
	{
		$messageArray = array(
			self::E_PARAM_REQUIRED		=> 'Error: param required!',
			self::E_UNKNOW				=> 'Error: unknow!',
			self::E_PARAM_DUPLICATE_KEY	=> 'Error: param faild ! duplicate key',
		);
		
		return isset($messageArray[$code]) ? $messageArray[$code] : '';
	}
}