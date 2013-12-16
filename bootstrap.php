<?php

	require_once(dirname(__FILE__) .'/lib/doctrine/doctrine.php');
	spl_autoload_register(array('Doctrine', 'autoload'));
	$manager = Doctrine_Manager::getInstance();


