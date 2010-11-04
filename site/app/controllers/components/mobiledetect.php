<?php 
class MobiledetectComponent extends Object
{
	public $isMobile = false;
	
	public function startup(&$controller)
	{
		$this->controller =& $controller;
		 ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APP . 'vendors' . DS);
	}
	
	public function detect()
	{
		App::import('Vendor', 'Mobi', array( 'file' => 'Mobi/Mtld/DA/Api.php'));
		
		$tree = Mobi_Mtld_DA_Api::getTreeFromFile(WWW_ROOT . 'json' . DS . 'Sample.json');
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
			$w_width = $properties['usableDisplayWidth'];
			$w_height = $properties['usableDisplayHeight'];
			$model = $properties['model'];
			$vendor = $properties['vendor'];
			$uri_tel = $properties['uriSchemeTel'];
			$this->isMobile = true;
			$this->controller->view = 'Theme';
			$this->controller->theme = 'mobile';
			$this->controller->layoutPath = 'mobile';
		}
		
	}
}
?>