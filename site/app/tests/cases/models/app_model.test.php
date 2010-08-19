<?php
App::import('Model', 'AppModel');
class AppModelTestCase extends CakeTestCase 
{
	public $AppModel = null;
	public $fixtures = null;

	public function startTest() 
	{
		$this->AppModel =& ClassRegistry::init('AppModel');
	}

	public function testAppModelInstance() 
	{
		$this->assertTrue(is_a($this->AppModel, 'AppModel'));
	}

	public function testDoctrineConnection() 
	{
		$this->assertTrue(!empty($this->AppModel->manager));
		$this->assertTrue(!empty($this->AppModel->doctrineConn));
		$testQuery = $this->AppModel->doctrineConn->prepare('SHOW TABLES');
		$testQuery->execute();
		$tables = $testQuery->fetchAll();
		$this->assertTrue(count($tables) > 0);
	}
}
?>