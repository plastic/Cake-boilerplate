<?php
/* Usuario Test cases generated on: 2010-11-03 18:11:44 : 1288816484*/
App::import('Model', 'Usuario');

class UsuarioTestCase extends CakeTestCase {
	var $fixtures = array('app.usuario');

	function startTest() {
		$this->Usuario =& ClassRegistry::init('Usuario');
	}
	
	public function testPlastic()
	{
		$this->assertTrue(is_object($this->Usuario));
		$usuario = $this->Usuario->find('all');
		$this->assertTrue(is_string($usuario[0]['Usuario']['id']));
	}

	function endTest() {
		unset($this->Usuario);
		ClassRegistry::flush();
	}

}
?>