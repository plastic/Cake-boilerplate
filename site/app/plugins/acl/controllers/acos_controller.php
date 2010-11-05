<?php
/**
 *
 * @author   Nicolas Rod <nico@alaxos.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.alaxos.ch
 */
class AcosController extends AclAppController {

	var $name = 'Acos';
	var $components = array('Acl');
	 
	function admin_index()
	{
	    
	}
	
	function admin_empty_acos($run = null)
	{
	    if(isset($run))
	    {
    		if($this->Aco->deleteAll(array('id > 0')))
    	    {
    	        $this->Session->setFlash(__d('acl', 'The ACO table has been cleared', true), 'flash_message', null, 'plugin_acl');
    	    }
    	    else
    	    {
    	        $this->Session->setFlash(__d('acl', 'The ACO table could not be cleared', true), 'flash_error');
    	    }
    	    
    	    $this->set('run', true);
	    }
	    else
	    {
	        $this->set('run', false);
	    }
	}
	
	function admin_build_acl($run = null)
	{
	    if(isset($run))
	    {
    		$log = array();
    
    		$aco =& $this->Acl->Aco;
    		$root = $aco->node('controllers');
    		if (!$root)
    		{
    			$aco->create(array('parent_id' => null, 'model' => null, 'alias' => 'controllers'));
    			$root = $aco->save();
    			$root['Aco']['id'] = $aco->id;
    			$log[] = 'Created Aco node for controllers';
    		}
    		else
    		{
    			$root = $root[0];
    		}
    
    		App::import('Core', 'File');
    		$Controllers = App :: objects('controller');
    		$appIndex = array_search('App', $Controllers);
    		if ($appIndex !== false )
    		{
    			unset($Controllers[$appIndex]);
    		}
    		
    		$baseMethods = get_class_methods('Controller');
    		$baseMethods[] = 'buildAcl';
    
    		$Plugins = $this->_getPluginControllerNames();
    		$Controllers = array_merge($Controllers, $Plugins);
    
    		// look at each controller in app/controllers
    		foreach ($Controllers as $ctrlName)
    		{
    			$methods = $this->_getClassMethods($this->_getPluginControllerPath($ctrlName));
    
    			// Do all Plugins First
    			if ($this->_isPlugin($ctrlName))
    			{
    			    $plugin_name = $this->_getPluginName($ctrlName);
    			    
    				$pluginNode = $aco->node('controllers/'. $plugin_name);
    				if (!$pluginNode)
    				{
    					$aco->create(array('parent_id' => $root['Aco']['id'], 'model' => null, 'alias' => $plugin_name));
    					$pluginNode = $aco->save();
    					$pluginNode['Aco']['id'] = $aco->id;
    					$log[] = 'Created Aco node for ' . $plugin_name . ' Plugin';
    				}
    			}
    			
    			// find / make controller node
    			$controllerNode = $aco->node('controllers/'.$ctrlName);
    			if (!$controllerNode)
    			{
    				if ($this->_isPlugin($ctrlName))
    				{
    				    
        				if($plugin_name == $this->_getPluginControllerName($ctrlName))
        			    {
        			        /*
        			         * If a controller with the same name as its plugin exists in the ACO table,
        			         * it may cause problems when a path to the node is looked up in the db,
        			         * as the query uses the alias to find nodes
        			         */
        			        continue;
        			    }
    				    
    					$pluginNode = $aco->node('controllers/' . $this->_getPluginName($ctrlName));
    					$aco->create(array('parent_id' => $pluginNode['0']['Aco']['id'], 'model' => null, 'alias' => $this->_getPluginControllerName($ctrlName)));
    					$controllerNode = $aco->save();
    					$controllerNode['Aco']['id'] = $aco->id;
    					$log[] = 'Created Aco node for ' . $ctrlName;
    				}
    				else
    				{
    					$aco->create(array('parent_id' => $root['Aco']['id'], 'model' => null, 'alias' => $ctrlName));
    					$controllerNode = $aco->save();
    					$controllerNode['Aco']['id'] = $aco->id;
    					$log[] = 'Created Aco node for ' . $ctrlName;
    				}
    			}
    			else
    			{
    				$controllerNode = $controllerNode[0];
    			}
    
    			foreach ($methods as $k => $method)
    			{
    			    /*
    			     * clean the methods. to remove those in Controller and private actions.
    			     */
    				if (strpos($method, '_', 0) === 0)
    				{
    					unset($methods[$k]);
    					continue;
    				}
    				if (in_array($method, $baseMethods))
    				{
    					unset($methods[$k]);
    					continue;
    				}
    				
    				/*
    				 * Create method node if it doesn't exist
    				 */
    				$methodNode = $aco->node('controllers/'.$ctrlName.'/'.$method);
    				if (!$methodNode)
    				{
    					$aco->create(array('parent_id' => $controllerNode['Aco']['id'], 'model' => null, 'alias' => $method));
    					$methodNode = $aco->save();
    					$log[] = 'Created Aco node for '. $ctrlName . '/' . $method;
    				}
    			}
    		}
    		
    		$this->set('logs', $log);
    		$this->set('run', true);
	    }
	    else
	    {
	        $this->set('run', false);
	    }
	}

	
}
?>