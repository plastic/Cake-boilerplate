<?php
class AppController extends Controller 
{
	public $helpers = array('Html', 'Form', 'Javascript', 'Text', 'Session', 'Image', 'Ajax', 'ScriptCombiner');
	public $components = array('Auth', 'RequestHandler', 'Email', 'Session', 'Cookie');
	
	public function beforeFilter()
	{
		#$this->Auth->allow('*');
		/*
		$this->Auth->authorize = 'actions';
		$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
		$this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');
		$this->Auth->loginRedirect = array('controller' => 'notes', 'action' => 'index');
		*/
	}
	/*
	public function beforerender()
	{
		if ( isset($this->params['url']['site']) ) 
		{
			setcookie('site', 'default', strtotime('+1 hour'));
			$this->Session->delete('device_id');
		} 
		else 
		{
			if ( !isset($_COOKIE['site']) || isset($this->params['url']['mobile'])) 
			{
				$this->Mobiledetect->startup($this);
				$this->Mobiledetect->detect();
			}
		}
	}
	*/
	
	public function _queueEmail($settings, $checkMailing = false) 
	{
		$default = array(
			'controller' => array(
				'base'    => $this->base,
				'webroot' => $this->webroot,
				'helpers' => $this->helpers
			),
			'settings' => array(
				'to'          => '',
				'from'        => 'My User <myuser@mywebsite.com.br>',
				'replyTo'     => 'myuser@mywebsite.com.br',
				'subject'     => '',
				'template'    => '',
				'delivery'    => 'smtp',
				'smtpOptions' => array(
					'port'     => 25,
					'host'     => 'smtp.mywebsite.com',
					'username' => 'myuser@mywebsite.com',
					'password' => 'mypass'
				),
				'charset'     => 'UTF-8'
			),
			'vars' => array()
		);
		$settings = Set::merge($default, $settings);
		
		$this->loadModel('Queue.QueuedTask');
		$this->QueuedTask->createJob('advanced_email', $settings);
		return true;
	}
	
	/*
	
	How to use on controller
	
	$this->_queueEmail( array(
		'settings' => array(
			'to'       => '<' . $look['User']['email'] . '>',
			'template' => $status,
			'subject'  => $subject
		),
		'vars' => array(
			'usuario' => $look['User']
		)
	));
	
	*/
}
?>