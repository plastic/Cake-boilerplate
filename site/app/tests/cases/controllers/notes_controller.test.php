<?php
/* Notes Test cases generated on: 2010-11-04 10:11:15 : 1288873275*/
App::import('Controller', 'Notes');

class TestNotesController extends NotesController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class NotesControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.note');

	function startTest() {
		$this->Notes =& new TestNotesController();
		$this->Notes->constructClasses();
	}

	function endTest() {
		unset($this->Notes);
		ClassRegistry::flush();
	}

	function testIndex() {

	}

	function testView() {

	}

	function testAdd() {

	}

	function testEdit() {

	}

	function testDelete() {

	}

}
?>