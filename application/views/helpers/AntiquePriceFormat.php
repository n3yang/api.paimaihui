<?php
/**
 *
 * @author Sair
 * @version 
 */
require_once 'Zend/View/Interface.php';
/**
 * PriceFormat helper
 *
 * @uses viewHelper Paimaihui_View_Helper
 */
class Paimaihui_View_Helper_AntiquePriceFormat
{
	/**
	 * @var Zend_View_Interface 
	 */
	public $view;
	/**
	 * 
	 */
	public function antiquePriceFormat ($price='', $status='')
	{
		if (!empty($price)) {
			return number_format($price);
		}
		if ($status==Application_Model_DbTable_Antique::STATUS_CANCEL) {
			return '撤拍';
		}
		if ($status==Application_Model_DbTable_Antique::STATUS_FAIL) {
			return '流拍';
		}
	}
	/**
	 * Sets the view field 
	 * @param $view Zend_View_Interface
	 */
	public function setView (Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}
