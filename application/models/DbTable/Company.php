<?php

class Application_Model_DbTable_Activity extends Zend_Db_Table_Abstract
{

    protected $_name = 'company';

	const IS_PUBLISHED_YES = 1;
	const IS_PUBLISHED_NO = 0;
	
}