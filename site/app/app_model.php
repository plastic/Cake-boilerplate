<?php
class AppModel extends Model 
{
	public $recursive = -1;
	
	#descomente isto para usar o doctrine
	#public $useTable = false;
	public $manager;
	public $doctrineConn;

	public function __construct($id = false, $table = null, $ds = null) 
	{
		parent::__construct($id, $table, $ds);
		#$this->__configureDoctrine();
	}

	public function __configureDoctrine() 
	{
		App::import('vendor', 'doctrine', array('file' => 'doctrine' . DS . 'lib' . DS . 'Doctrine.php'));
		spl_autoload_register(array('Doctrine', 'autoload'));
		$this->manager = Doctrine_Manager::getInstance();
		$this->__getDoctrineCompatibleDataSource();
	}

	public function __getDoctrineCompatibleDataSource() 
	{
		$dataSource = $this->getDataSource();
		$doctrineDsc = 
		$dataSource->config['driver'].':dbname='.
		$dataSource->config['database'].';host='.
		$dataSource->config['host'];
		$user = $dataSource->config['login'];
		$passwd = $dataSource->config['password'];
		$dbh = new PDO($doctrineDsc, $user, $passwd);
		$this->doctrineConn = Doctrine_Manager::connection($dbh);
	}
	
	// public function __construct($id = false, $table = null, $ds = null) {
	// 	if (preg_match('/^(www|homer)$/', $_SERVER['HTTP_HOST']))
	// 		$this->useDbConfig = "production";
	// 	
	// 	parent::__construct($id, $table, $ds);
	// }
}
?>