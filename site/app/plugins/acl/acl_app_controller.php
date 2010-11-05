<?php
/**
 *
 * @author   Nicolas Rod <nico@alaxos.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.alaxos.ch
 */
class AclAppController extends AppController
{
    var $components = array('Acl', 'Auth', 'Session');

    function beforeFilter()
	{
	    parent :: beforeFilter();
	    
		$this->_check_config();
	}
    
	private function _check_config()
	{
	    $role_model_name = Configure :: read('acl.aro.role.model');
	    
		if(!empty($role_model_name))
		{
	    	$this->set('role_model_name',    Configure :: read('acl.aro.role.model'));
	    	$this->set('role_display_field', Configure :: read('acl.aro.role.display_field'));
	    	$this->set('role_fk_name',       strtolower(Configure :: read('acl.aro.role.model')) . '_id');
	    	$this->set('user_model_name',    Configure :: read('acl.aro.user.model'));
	    	
	    	$this->_authorize_admins();
	    	
	    	if($this->name != 'Acos' || $this->action != 'admin_build_acl')
	    	{
	    	    $this->_check_missing_acos();
	    	}
		}
		else
		{
			$this->Session->setFlash(__('The role model name is unknown. The ACL plugin bootstrap.php file has to be loaded in order to work. (see the README file)', true), 'flash_error');
		}
	}
	
	private function _authorize_admins()
	{
		$authorized_role_ids = Configure :: read('acl.role.access_plugin_role_ids');
		$authorized_user_ids = Configure :: read('acl.role.access_plugin_user_ids');

		$model_role_fk = strtolower(Configure :: read('acl.aro.role.model')) . '_id';
		
	    if(in_array($this->Auth->user($model_role_fk), $authorized_role_ids)
	       || in_array($this->Auth->user('id'), $authorized_user_ids))
	    {
	        $this->Auth->allow('*');
	    }
	}
	
	
	function _check_missing_acos_tmp_file()
	{
	    if(is_writable(APP . 'plugins' . DS . 'acl' . DS . 'tmp'))
	    {
	        $file = new File(APP . 'plugins' . DS . 'acl' . DS . 'tmp' . DS . 'controllers_hashes.txt', true);
	        return $file->exists();
	    }
	    else
	    {
	        $this->Session->setFlash(__('the ACL plugin "tmp" directory is not writable', true), 'flash_error');
	        return false;
	    }
	}
	
	function _get_stored_controllers_hashes()
	{
	    $file = new File(APP . 'plugins' . DS . 'acl' . DS . 'tmp' . DS . 'controllers_hashes.txt');
		$file_content = $file->read();
		
		if(!empty($file_content))
		{
			$stored_controller_hashes = unserialize($file_content);
		}
		else
		{
			$stored_controller_hashes = array();
		}
		
		return $stored_controller_hashes;
	}
	
	function _get_current_controllers_hashes()
	{
	    $controllers = App :: objects('controller');
		$plugin_controllers = $this->_getPluginControllerNames();
		$controllers = array_merge($controllers, $plugin_controllers);
		
		$current_controller_hashes = array();
		foreach($controllers as $controller)
		{
			if($this->_isPlugin($controller))
			{
				$ctler_file = new File(APP . 'plugins' . DS . 'acl' . DS . 'controllers' . DS . Inflector :: underscore($this->_getPluginControllerName($controller)) . '_controller.php');
				$current_controller_hashes[$controller] = $ctler_file->md5();
			}
			else
			{
				$ctler_file = new File(APP . 'controllers' . DS . Inflector :: underscore($controller) . '_controller.php');
				if(!$ctler_file->exists())
				{
					$ctler_file = new File(APP . DS . Inflector :: underscore($controller) . '_controller.php');
				}
				$current_controller_hashes[$controller] = $ctler_file->md5();
			}
		}
		
		return $current_controller_hashes;
	}
	
	function _check_missing_acos()
	{
	    if($this->_check_missing_acos_tmp_file())
	    {
	        $missing_aco_nodes = array();
	        
    		$stored_controller_hashes  = $this->_get_stored_controllers_hashes();
    		$current_controller_hashes = $this->_get_current_controllers_hashes();
    		
    		/*
    		 * Store current controllers hashes on disk
    		 */
    		$file = new File(APP . 'plugins' . DS . 'acl' . DS . 'tmp' . DS . 'controllers_hashes.txt');
    		$file->write(serialize($current_controller_hashes));
    		
    		/*
    		 * Check what controllers have changed
    		 */
    		$updated_controllers = array_keys(Set :: diff($current_controller_hashes, $stored_controller_hashes));
    		
    		//DEBUG
    		//$updated_controllers = array('Acl/Acos');
    		
    		if(!empty($updated_controllers))
    		{
    			$aco =& $this->Acl->Aco;
    			
    			foreach($updated_controllers as $controller_name)
    			{
    			    $methods = $this->_getCleanedClassMethods($controller_name);
    			    
    			    $aco =& $this->Acl->Aco;
    			    foreach($methods as $method)
    			    {
    			        $methodNode = $aco->node('controllers/' . $controller_name . '/' . $method);
    			        if(empty($methodNode))
    			        {
    			            $missing_aco_nodes[] = $controller_name . '/' . $method;
    			        }
    			    }
    			}
    		}
    		
    		if(count($missing_aco_nodes) > 0)
    		{
    		    //debug($missing_aco_nodes);
    		    $this->set('missing_aco_nodes', $missing_aco_nodes);
    		    $this->render('/acos/admin_acos_missing');
    		}
	    }
	}
	
	
	/*
	 * Return the class methods,
	 * but without methods starting with "_" and methods of the "Controller" class
	 */
	function _getCleanedClassMethods($ctrlName = null)
	{
	    if($this->_isPlugin($ctrlName))
	    {
	        $ctrlName = $this->_getPluginControllerName($ctrlName);
	    }
	    
	    $baseMethods = get_class_methods('Controller');
	    $methods = $this->_getClassMethods($ctrlName);
	    
	    $ctrl_cleaned_methods = array();
	    foreach($methods as $method)
	    {
	        if(!in_array($method, $baseMethods) && strpos($method, '_') !== 0)
			{
			    $ctrl_cleaned_methods[] = $method;
			}
	    }
	    
	    return $ctrl_cleaned_methods;
	}
	
	function _getClassMethods($ctrlName = null)
	{
		App::import('Controller', $ctrlName);
		if (strlen(strstr($ctrlName, '.')) > 0) {
			// plugin's controller
			$num = strpos($ctrlName, '.');
			$ctrlName = substr($ctrlName, $num+1);
		}
		$ctrlclass = $ctrlName . 'Controller';
		$methods = get_class_methods($ctrlclass);

		// Add scaffold defaults if scaffolds are being used
		$properties = get_class_vars($ctrlclass);
		if (array_key_exists('scaffold', $properties) && $properties['scaffold'] !== false)
		{
			if($properties['scaffold'] == 'admin')
			{
				$methods = array_merge($methods, array('admin_add', 'admin_edit', 'admin_index', 'admin_view', 'admin_delete'));
			}
			else
			{
				$methods = array_merge($methods, array('add', 'edit', 'index', 'view', 'delete'));
			}
		}
		
		return $methods;
	}
	
	function _getPluginControllerPath($ctrlName = null)
	{
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[0] . '.' . $arr[1];
		} else {
			return $arr[0];
		}
	}
	
	function _isPlugin($ctrlName = null)
	{
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) > 1) {
			return true;
		} else {
			return false;
		}
	}
	
	function _getPluginName($ctrlName = null)
	{
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[0];
		} else {
			return false;
		}
	}

	function _getPluginControllerName($ctrlName = null)
	{
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[1];
		} else {
			return false;
		}
	}

    
    /**
     * Get the names of the plugin controllers ...
     *
     * This function will get an array of the plugin controller names, and
     * also makes sure the controllers are available for us to get the
     * method names by doing an App::import for each plugin controller.
     *
     * @return array of plugin names.
     *
     */
	function _getPluginControllerNames()
	{
		App::import('Core', 'File', 'Folder');
		$paths = Configure::getInstance();
		$folder =& new Folder();
		$folder->cd(APP . 'plugins');

		// Get the list of plugins
		$Plugins = $folder->read();
		$Plugins = $Plugins[0];
		$arr = array();

		// Loop through the plugins
		foreach($Plugins as $pluginName) 
		{
			// Change directory to the plugin
			$didCD = $folder->cd(APP . 'plugins'. DS . $pluginName . DS . 'controllers');
			
			if(!empty($didCD))
			{
				// Get a list of the files that have a file name that ends
				// with controller.php
				$files = $folder->findRecursive('.*_controller\.php');
	
				// Loop through the controllers we found in the plugins directory
				foreach($files as $fileName) {
					// Get the base file name
					$file = basename($fileName);
	
					// Get the controller name
					$file = Inflector::camelize(substr($file, 0, strlen($file)-strlen('_controller.php')));
					if (!preg_match('/^'. Inflector::humanize($pluginName). 'App/', $file)) {
						if (!App::import('Controller', $pluginName.'.'.$file)) {
							debug('Error importing '.$file.' for plugin '.$pluginName);
						} else {
							/// Now prepend the Plugin name ...
							// This is required to allow us to fetch the method names.
							$arr[] = Inflector::humanize($pluginName) . "/" . $file;
						}
					}
				}
			}
		}
		
		return $arr;
	}
	
	
	/**
	 * return an array of all existings actions of both app/controllers and app/plugins/.../controllers
	 *
	 * If a plugin controller with the same name as the plugin itself is found, it is ignored
	 * as the queries to retrieve the ACOs tree are broken in such as case.
	 */
	function _get_all_actions()
	{
	    if(!isset($this->{Configure :: read('acl.aro.role.model')}))
	    {
	        $this->loadModel(Configure :: read('acl.aro.role.model'));
	    }
	    
	    $this->{Configure :: read('acl.aro.role.model')}->recursive = -1;
	    $roles = $this->{Configure :: read('acl.aro.role.model')}->find('all');
	    
	    $methods = array();
	    $controllers = App :: objects('controller');
	    
	    /*
	     * Takes care of App controllers actions
	     */
	    foreach($controllers as $ctrlName)
	    {
	    	if($ctrlName != 'App')
	    	{
		    	$ctrl_cleaned_methods = $this->_getCleanedClassMethods($this->_getPluginControllerPath($ctrlName));
		    	
		    	//$methods = array_merge($methods, $ctrl_methods);
		    	sort($ctrl_cleaned_methods);
		    	
	    		foreach($ctrl_cleaned_methods as $ctrl_cleaned_method)
		    	{
		    		$permissions = array();
			    		
			    	foreach($roles as $role)
			    	{
			    	    $aro_node = $this->Acl->Aro->node($role);
    		    	    if(!empty($aro_node))
    		    	    {
    			    	    $aco_node = $this->Acl->Aco->node($ctrlName . '/' . $ctrl_cleaned_method);
    			    	    if(!empty($aco_node))
    			    	    {
        			    		$authorized = $this->Acl->check($role, $ctrlName . '/' . $ctrl_cleaned_method);
        			    		
        			    		$permissions[$role[Configure :: read('acl.aro.role.model')]['id']] = $authorized ? 1 : 0 ;
    			    	    }
    		    	    }
    		    	    else
    		    	    {
    		    	        /*
    		    	         * No check could be done as the ARO is missing
    		    	         */
    		    	        $permissions[$role[Configure :: read('acl.aro.role.model')]['id']] = -1;
    		    	    }
			    	}

			    	$methods['app'][$ctrlName][] = array('name' => $ctrl_cleaned_method, 'permissions' => $permissions);
		    	}
		    }
	    }
	    
	    ksort($methods['app']);
	    
	    
	    /*
	     * Takes care of plugins actions
	     */
	    $plugin_ctrl_names = $this->_getPluginControllerNames();
	    foreach($plugin_ctrl_names as $plugin_ctrl_name)
	    {
	        $plugin_name = $this->_getPluginName($plugin_ctrl_name);
	        $ctrlName    = $this->_getPluginControllerName($plugin_ctrl_name);
	        
	        //debug($plugin_ctrl_name . '=> ' . $plugin_name . ' ' . $ctrlName);
	        
	        if($plugin_name == $ctrlName)
		    {
		        /*
		         * If a controller with the same name as its plugin exists in the ACO table,
		         * it may cause problems when a path to the node is looked up in the db,
		         * as the query uses the alias to find nodes
		         */
		        continue;
		    }
	        
	        $plugin_ctrl_cleaned_methods = $this->_getCleanedClassMethods($this->_getPluginControllerPath($ctrlName));
	        
	    	//$methods = array_merge($methods, $ctrl_methods);
	    	sort($plugin_ctrl_cleaned_methods);
	    	
	    	//debug($plugin_ctrl_cleaned_methods);
	    	
	    	foreach($plugin_ctrl_cleaned_methods as $ctrl_cleaned_method)
	    	{
	    		$permissions = array();
		    		
		    	foreach($roles as $role)
		    	{
		    	    $aro_node = $this->Acl->Aro->node($role);
		    	    if(!empty($aro_node))
		    	    {
    		    	    $aco_node = $this->Acl->Aco->node($ctrlName . '/' . $ctrl_cleaned_method);
    		    	    if(!empty($aco_node))
    		    	    {
        		    		$authorized = $this->Acl->check($role, $plugin_name . '/' . $ctrlName . '/' . $ctrl_cleaned_method);
        		    		
        		    		$permissions[$role[Configure :: read('acl.aro.role.model')]['id']] = $authorized ? 1 : 0 ;
    		    	    }
		    	    }
		    	    else
		    	    {
		    	        /*
		    	         * No check could be done as the ARO is missing
		    	         */
		    	        $permissions[$role[Configure :: read('acl.aro.role.model')]['id']] = -1;
		    	    }
		    	}

		    	$methods['plugin'][$plugin_name][$ctrlName][] = array('name' => $ctrl_cleaned_method, 'permissions' => $permissions);
	    	}
	    }
	    
	    ksort($methods['plugin']);
	    
	    return $methods;
	}

	
    function _get_passed_aco_path()
	{
	    $aco_path  = isset($this->params['named']['plugin']) ? $this->params['named']['plugin'] : '';
        $aco_path .= empty($aco_path) ? $this->params['named']['controller'] : '/' . $this->params['named']['controller'];
        $aco_path .= '/' . $this->params['named']['action'];
        
        return $aco_path;
	}

	function _set_aco_variables()
	{
        $this->set('plugin', isset($this->params['named']['plugin']) ? $this->params['named']['plugin'] : '');
        $this->set('controller_name', $this->params['named']['controller']);
        $this->set('action', $this->params['named']['action']);
	}

	function _return_to_referer()
	{
	    $this->redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : array('action' => 'admin_index'));
	}
	
	
}
?>