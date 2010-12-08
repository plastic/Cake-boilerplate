<?php 
class MobiledetectComponent extends Object
{
	public $isMobile = false;
	public $components = array('Session', 'Cookie');
	
	public function startup(&$controller)
	{
		$this->controller =& $controller;
		$this->Session = new SessionComponent();
		Cache::set(
			array(
				'engine' => 'File',
				'duration' => '+1 hour',
				'path' => CACHE . 'mobile' . DS,
				'prefix' => false
			)
		);
		ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APP . 'vendors' . DS);
	}
	
	public function detect()
	{
		if ($this->checkCache()) 
			return true;
			
		App::import('Vendor', 'Mobi', array( 'file' => 'Mobi/Mtld/DA/Api.php'));
		$tree = Mobi_Mtld_DA_Api::getTreeFromFile( WWW_ROOT . 'json' . DS . 'Sample.json');
		$properties = Mobi_Mtld_DA_Api::getProperties($tree, $_SERVER['HTTP_USER_AGENT']);
		$mobileDevice = $properties['mobileDevice'];
		
		if ( ! $mobileDevice == 1)
		{
			if ( !isset($w_width) )
			{
				$w_width = '320';
				$vendor = 'None';
			}
		}
		else
		{
			$results = Cache::read('device_mobile_' . $properties['id']);
			if ($results !== false) 
				$this->setMobile($results);
			else
			{
				$this->setCache($properties);
				$this->setMobile($properties);
			}
		}
	}
	
	public function setMobile($settings=null)
	{
		$this->isMobile = true;
		$this->controller->view = 'Theme';
		$this->controller->theme = 'mobile';
		$this->controller->layoutPath = 'mobile';
		
		Configure::write('Mobile', array(
			'modelo' => $settings['model'],
			'vendor' => $settings['vendor'],
			'uri_tel' => $settings['uriSchemeTel'],
			'width' => $settings['usableDisplayWidth'],
			'height' => $settings['usableDisplayHeight']
		));
		
		$this->Session->write('device_id', $settings['id']);
	}
	
	public function setCache($settings=null)
	{
		Cache::write('device_mobile_' . $settings['id'], $settings);
		$this->Session->write('device_id', $settings['id']);
	}
	
	public function checkCache()
	{
		if ($this->Session->check('device_id')) 
		{
			$device = Cache::read('device_mobile_' . $this->Session->read('device_id'));
			if ($device !== false) 
			{
				$this->setMobile($device);
				return true;
			}
		}
		return false;
	}
	
}
?>