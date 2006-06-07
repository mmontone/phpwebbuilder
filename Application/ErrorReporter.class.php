<?php

class ErrorReporter {
	var $configuration;


    function ErrorReporter($configuration=array()) {

    }

    function report($params) {}
}

class SimpleErrorReporter extends ErrorReporter 
{
	function report($params) {
		echo "<html><head></head><body>\n";
		echo "<h1>" . $params['message'] . "</h1>";
		echo "</body></html>";
		print_backtrace();
		die;
	}
}

class InvalidComponentReporter extends ErrorReporter
{
	function report($params) {
		echo "<html><head></head><body>\n";
		echo "<h1>The component is invalid: " . $params['component'] . "</h1>\n";
		echo "</body></html>";
                print_backtrace();
		die;
	}
}

class InvalidActionReporter extends ErrorReporter
{
	function report($params) {
		echo "<html><head></head><body>\n";
		echo "<h1>The action is invalid: " . $params['action']->action_selector. "</h1>\n";
		echo "</body></html>";
		print_backtrace();
		die;
	}
}
?>