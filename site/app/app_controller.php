<?php
class AppController extends Controller 
{
	public $helpers = array('Html', 'Form', 'Javascript', 'Text', 'Session', 'Image', 'Ajax', 'AssetCompress.AssetCompress');
	public $components = array('Auth', 'RequestHandler', 'Email', 'Session', 'Cookie');
	
	public function beforeFilter()
	{
		$this->Auth->allow('display');
	}
}
?>