<?php
/**
 *
 * @author Sair
 * @version 
 */
require_once 'Zend/View/Interface.php';
/**
 * EstimateFormat helper
 *
 * @uses viewHelper Paimaihui_views_helpers
 */
class Paimaihui_View_Helper_AntiqueEstimateFormat
{
	/**
	 * @var Zend_View_Interface 
	 */
	public $view;
	/**
	 * 
	 */
	public function antiqueEstimateFormat ($estimate='', $low='', $high='', $currency='RMB')
	{
		if (!empty($estimate)) {
			if (preg_match('/^\d+/', $v)) {
				return $currency . ' ' . $estimate;
			} else {
				return $estimate;
			}
		}
		if ($low && $high) {
			return $currency.' '.number_format($low).'-'.number_format($high);
		}
		return '无底价';
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
