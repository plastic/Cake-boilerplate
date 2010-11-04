<?php
/* Note Test cases generated on: 2010-11-04 10:11:15 : 1288873275*/
App::import('Model', 'Note');

class NoteTestCase extends CakeTestCase {
	var $fixtures = array('app.note');

	function startTest() {
		$this->Note =& ClassRegistry::init('Note');
	}

	function endTest() {
		unset($this->Note);
		ClassRegistry::flush();
	}

}
?>