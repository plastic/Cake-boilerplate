<?php
class AppController extends Controller 
{
	public $helpers = array('Html', 'Form', 'Javascript', 'Text', 'Session', 'Image', 'Ajax', 'AssetCompress.AssetCompress');
	public $components = array('Auth', 'RequestHandler', 'Email', 'Session', 'Cookie');
	
	public function beforeFilter()
	{
		$this->Auth->allow('display');
	}
	
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