<?php
require_once '../simpletest/reporter.php';

class PWBHtmlTestsReporter extends HtmlReporter {
	var $testlist = array();

	function PWBHtmlTestsReporter() {
		$this->HtmlReporter();
	}

	function _getCss() {
		//$css =& parent::_getCss();
		$css .= ".pass{color:green; font-weight:bold}\n";
		$css .= ".fail{color:red; font-weight:bold}";
		return $css;
	}
	function paintFail($message) {
		//parent :: paintFail($message);
		/*$breadcrumb = $this->getTestList();
		array_shift($breadcrumb);
		print implode("-&gt;", $breadcrumb);*/
		$this->paintContext();
		print "$message ";
		print "<span class=\"fail\">[ Failed ]</span> \n <br /> ";
	}

	function paintContext() {
		$testlist = $this->getTestList();
		if ($this->testlist[1] != $testlist[1]) {
			$this->testlist[1] = $testlist[1];
			print '<h2>' . $this->testlist[1] . '</h2>';

		}

		if ($this->testlist[2] != $testlist[2]) {
			$this->testlist[2] = $testlist[2];
			print '<h3>' . $this->testlist[2] . '</h3>';
		}
	}

	function paintPass($message) {
		//parent :: paintPass($message);
		$this->paintContext();
		print "$message ";
		print "<span class=\"pass\">[ OK ]</span> \n <br />";
	}
}
?>