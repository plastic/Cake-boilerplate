<?php
class AppController extends Controller 
{
	public $helpers = array('Html', 'Form', 'Javascript', 'Text', 'Session', 'Image', 'Ajax', 'Asset');
	public $components = array('Auth', 'RequestHandler', 'Email', 'Session', 'Cookie');
	
}
?>