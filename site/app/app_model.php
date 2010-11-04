<?php

# To fast
# App::import('Lib', 'LazyModel.LazyModel');
# class AppModel extends LazyModel

class AppModel extends Model 
{
	public $recursive = -1;
	
	#descomente isto para usar o doctrine
	#public $useTable = false;
	public $manager;
	public $doctrineConn;

	public function __construct($id = false, $table = null, $ds = null) 
	{
		if ( isset($_SERVER['HTTP_HOST']) ) {
			if ( preg_match('/^(localhost)$/', $_SERVER['HTTP_HOST']) ) :
				$this->useDbConfig = "default";
			endif;
		} else {
			$this->useDbConfig = "default";
		}
		
		# $this->__configureDoctrine(); Apenas com Doctrine
		parent::__construct($id, $table, $ds);
	}
	
	/*
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
	*/
}
?>