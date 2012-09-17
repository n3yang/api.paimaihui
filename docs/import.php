<?php

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(realpath(__DIR__).'/../library'),
    get_include_path(),
)));


require_once 'Zend/Db.php';

