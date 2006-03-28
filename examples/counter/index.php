<?php
/*
 * Created on 01-mar-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once dirname(__FILE__) . '/components/Counter.class.php';

 class CounterApplication extends Application 
 {
 	function &set_root_component() {
 		return new Counter;
 	}
 }
 
 
?>
