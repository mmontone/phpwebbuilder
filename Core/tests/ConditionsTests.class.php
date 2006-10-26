<?php

class ConditionA extends PWBCondition {

}

class ConditionsTests extends UnitTestCase {

    function ConditionsTests() {
    	$this->UnitTestCase('Conditions tests');
    }


    function testConditionMatch() {
    	$h =& new PWBConditionHandler('ConditionA', new FunctionObject($this, 'handleConditionA'));
    	$this->assertTrue($h->matches(new ConditionA));
    }

    function testConditions1() {
		$scope =& enter_scope();
		$scope->addConditionHandler('ConditionA', new FunctionObject($this, 'handleConditionA'));

		$this->buggyFunction();
		if (!check_scope()) {
			return;
		}

		$this->assertTrue(false,'This should not be executed!!!!');
		leave_scope();
    }

    function buggyFunction() {
    	$cond =& new ConditionA(array('message' => 'This is the condition A'));
    	$cond->raise();
    }

    function handleConditionA(&$cond) {
    	$this->assertTrue(true, 'Handling condition A');
    }

    function testConditions2() {
		$scope =& enter_scope();
		$scope->addConditionHandler('ConditionA', new FunctionObject($this, 'handleConditionA2'));

		$this->nonBuggyFunction();
		if (!check_scope()) {
			return;
		}

		$this->assertTrue(true,'This should be executed!!!!');
		leave_scope();
    }

    function handleConditionA2(&$cond) {
    	$this->assertTrue(false, 'This should not be executed');
    }

    function nonBuggyFunction() {
    	$this->assertTrue(true, 'Entering non buggy function');
    }

    function testConditions3() {
		$scope =& enter_scope();
		$scope->addConditionHandler('ConditionA', new FunctionObject($this, 'handleConditionA2'));

		$this->nonBuggyFunction();
		if (!check_scope()) {
			return;
		}

		$this->assertTrue(true,'This should be executed!!!!');
		leave_scope();

		$scope =& enter_scope();
		$scope->addConditionHandler('ConditionA', new FunctionObject($this, 'handleConditionA'));

		$this->buggyFunction();
		if (!check_scope()) {
			return;
		}

		$this->assertTrue(false,'This should not be executed!!!!');
		leave_scope();
    }

    function testConditions4() {
		$scope =& enter_scope();
		$scope->addConditionHandler('ConditionA', new FunctionObject($this, 'handleConditionA'));

		$this->buggyFunction2();
		if (!check_scope()) {
			return;
		}

		$this->assertTrue(false,'This should not be executed!!!!');
		leave_scope();
    }

    function buggyFunction2() {
    	$scope =& enter_scope();
    	$this->buggyFunction();
    	if (!check_scope()) {
			return;
		}

		$this->assertTrue(false,'This should not be executed!!!!');
		leave_scope();
    }

    function testConditions5() {
		$scope =& enter_scope();

		$this->buggyFunction3();
		if (!check_scope()) {
			return;
		}

		$this->assertTrue(true,'This should be executed!!!!');
		leave_scope();
    }

    function buggyFunction3() {
    	$scope =& enter_scope();
    	$scope->addConditionHandler('ConditionA', new FunctionObject($this, 'handleConditionA'));
    	$this->buggyFunction();
    	if (!check_scope()) {
			return;
		}

		$this->assertTrue(false,'This should not be executed!!!!');
		leave_scope();
    }

    function testConditions6() {
		$scope =& enter_scope();

		$this->buggyFunction();
		if (!check_scope()) {
			return;
		}

		$this->assertTrue(false,'This should not be executed!!!!');
		leave_scope();
    }

    function testConditions7() {
		$scope =& enter_scope();
		$scope->addConditionHandler('ConditionA', new FunctionObject($this, 'handleConditionA'));

		$this->buggyFunction3();
		if (!check_scope()) {
			return;
		}
		$this->assertTrue(true,'This should be executed!!!!');

		$this->buggyFunction();
		if (!check_scope()) {
			return;
		}
		$this->assertTrue(false,'This should not be executed!!!!');

		leave_scope();
    }

    function testConditions8() {
		$scope =& enter_scope();

		// Handlers don't get ordered.
		$scope->addConditionHandler('ConditionA', new FunctionObject($this, 'handleConditionA'));
		$scope->addConditionHandler('PWBCondition', new FunctionObject($this, 'handleConditionA2'));

		$this->buggyFunction();
		if (!check_scope()) {
			return;
		}
		$this->assertTrue(false,'This should not be executed!!!!');

		leave_scope();
    }

    function testConditions9() {
		$scope =& enter_scope();

		$scope->addConditionHandler('ConditionA', new FunctionObject($this, 'handleConditionA2'));

		$res = $this->nonBuggyFunction2();
		if (!check_scope()) {
			return;
		}
		$this->assertTrue($res == 2, 'Result matches');

		leave_scope();
    }

    function nonBuggyFunction2() {
    	return 2;
    }
}


?>