<?php
/**
 *
 * @author   Nicolas Rod <nico@alaxos.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.alaxos.ch
 */
class ArosController extends AclAppController {

	var $name = 'Aros';
	var $uses = array('Aro');
	var $components = array('RequestHandler');
	var $helpers = array('Js' => array('Jquery'));
	
	var $paginate = array(
        'limit' => 25,
        'order' => array(
            'display_name' => 'asc'
        ));
	
	function beforeFilter()
	{
	    $this->loadModel(Configure :: read('acl.aro.role.model'));
	    $this->loadModel(Configure :: read('acl.aro.user.model'));
	    
	    parent :: beforeFilter();
	}
        
	
	function admin_index()
	{
	    
	}
	
	function admin_check($run = null)
	{
		$user_model_name = Configure :: read('acl.aro.user.model');
	    $role_model_name = Configure :: read('acl.aro.role.model');
	    
	    $missing_aros = array('roles' => array(), 'users' => array());
	    
		$roles = $this->{$role_model_name}->find('all');
		foreach($roles as $role)
		{
			/*
			 * Check if ARO for role exist
			 */
			$aro = $this->Aro->find('first', array('conditions' => array('model' => $role_model_name, 'foreign_key' => $role[$role_model_name]['id'])));
			
			if($aro === false)
			{
				$missing_aros['roles'][] = $role;
			}
		}
		
		$this->{$user_model_name}->virtualFields = array('display_name' => Configure :: read('acl.user.display_name'));
		$users = $this->{$user_model_name}->find('all');
		foreach($users as $user)
		{
			/*
			 * Check if ARO for user exist
			 */
			$aro = $this->Aro->find('first', array('conditions' => array('model' => $user_model_name, 'foreign_key' => $user[$user_model_name]['id'])));
			
			if($aro === false)
			{
				$missing_aros['users'][] = $user;
			}
		}
		
		
		if(isset($run))
		{
			$this->set('run', true);
			
			/*
			 * Complete roles AROs
			 */
			if(count($missing_aros['roles']) > 0)
			{
				foreach($missing_aros['roles'] as $k => $role)
				{
					$this->Aro->create(array('parent_id' 		=> null,
												'model' 		=> $role_model_name,
												'foreign_key' 	=> $role[$role_model_name]['id'],
												'alias'			=> $role[$role_model_name][Configure :: read('acl.aro.role.display_field')]));
					
					if($this->Aro->save())
					{
						unset($missing_aros['roles'][$k]);
					}
				}
			}
			
			/*
			 * Complete users AROs
			 */
			if(count($missing_aros['users']) > 0)
			{
				foreach($missing_aros['users'] as $k => $user)
				{
					/*
					 * Find ARO parent for user ARO
					 */
					$parent_id = $this->Aro->field('id', array('model' => $role_model_name, 'foreign_key' => $user[$user_model_name][strtolower(Configure :: read('acl.aro.role.model')) . '_id']));
					
					if($parent_id !== false)
					{
						$this->Aro->create(array('parent_id' 		=> $parent_id,
													'model' 		=> $user_model_name,
													'foreign_key' 	=> $user[$user_model_name]['id'],
													'alias'			=> $user[$user_model_name]['display_name']));
						
						if($this->Aro->save())
						{
							unset($missing_aros['users'][$k]);
						}
					}
				}
			}
		}
		else
		{
			$this->set('run', false);
		}
		
		$this->set('missing_aros', $missing_aros);
		
	}
	
	function admin_users()
	{
	    $user_model_name = Configure :: read('acl.aro.user.model');
	    $role_model_name = Configure :: read('acl.aro.role.model');
	    
	    $this->{$role_model_name}->recursive = -1;
	    $roles = $this->{$role_model_name}->find('all');
	    
	    $this->{$user_model_name}->recursive = -1;
	    $this->{$user_model_name}->virtualFields = array('display_name' => Configure :: read('acl.user.display_name'));
	    $users = $this->paginate($user_model_name);
	    
	    $missing_aro = false;
	    
	    foreach($users as &$user)
	    {
	    	$aro = $this->Acl->Aro->find('first', array('conditions' => array('model' => $user_model_name, 'foreign_key' => $user[$user_model_name]['id'])));
	    	
	        if($aro !== false)
	        {
	            $user['Aro'] = $aro['Aro'];
	        }
	        else
	        {
	            $missing_aro = true;
	        }
	    }
	    
	    $this->set('roles', $roles);
	    $this->set('users', $users);
	    $this->set('missing_aro', $missing_aro);
	}
	
	function admin_update_user_role()
	{
	    $user_model_name = Configure :: read('acl.aro.user.model');
	    
        $data = array($user_model_name => array('id' => $this->params['named']['user'], strtolower(Configure :: read('acl.aro.role.model')) . '_id' => $this->params['named']['role']));
	    
	    if($this->{$user_model_name}->save($data))
	    {
	        $this->Session->setFlash(__d('acl', 'The user role has been updated', true), 'flash_message', null, 'plugin_acl');
	    }
	    else
	    {
	        $this->Session->setFlash(__d('acl', 'The user role could not be updated', true), 'flash_error', null, 'plugin_acl');
	    }

	    $this->_return_to_referer();
	}
	
	
	function admin_role_permissions()
	{
	    $role_model_name = Configure :: read('acl.aro.role.model');
	    
	    $this->{$role_model_name}->recursive = -1;
	    $roles = $this->{$role_model_name}->find('all');
	    
	    $actions = $this->_get_all_actions();
	    
	    $this->set('roles', $roles);
	    $this->set('actions', $actions);
	}
	
	function admin_user_permissions($user_id = null)
	{
	    $user_model_name = Configure :: read('acl.aro.user.model');
	    $role_model_name = Configure :: read('acl.aro.role.model');
	    
		$this->{$user_model_name}->virtualFields = array('display_name' => Configure :: read('acl.user.display_name'));
		
	    if(empty($user_id))
	    {
	        $users = $this->paginate($user_model_name);
	        
	        $this->set('users', $users);
	    }
	    else
	    {
	        $this->{$role_model_name}->recursive = -1;
	        $roles = $this->{$role_model_name}->find('all');
	        
	        $user = $this->{$user_model_name}->read(null, $user_id);
	        
	        /*
             * Check if the user exists in the ARO table
             */
            $user_aro = $this->Acl->Aro->node($user);
            if(empty($user_aro))
            {
                if(empty($this->{$user_model_name}->virtualFields['display_name']))
                {
                    $this->{$user_model_name}->virtualFields['display_name'] = Configure :: read('acl.user.display_name');
                }
                
                $display_user = $this->{$user_model_name}->find('first', array('conditions' => array($user_model_name . '.id' => $user_id)));
                $this->Session->setFlash(sprintf(__d('acl', "The user '%s' does not exist in the ARO table", true), $display_user[$user_model_name]['display_name']), 'flash_error');
            }
	        
	        $all_actions = $this->_get_all_actions();
	        
	        foreach($all_actions['app'] as $ctrl_name => &$actions)
	        {
	            foreach($actions as &$action)
	            {
	                $aco_path = $ctrl_name . '/' . $action['name'];
	                $aco_node = $this->Acl->Aco->node($aco_path);
	                
	                if(!empty($user_aro) && !empty($aco_node))
	                {
	                    $action['user_permissions'] = array($user_id => $this->Acl->check($user, $aco_path) ? 1 : 0);
	                }
	                else
	                {
	                    $action['user_permissions'] = array($user_id => -1);
	                }
	            }
	        }
	        foreach($all_actions['plugin'] as $plugin_name => &$ctrlers)
	        {
	            foreach($ctrlers as $ctrl_name => &$ctrler_actions)
	            {
	                foreach($ctrler_actions as &$action)
	                {
	                    $aco_path = $plugin_name . '/' . $ctrl_name . '/' . $action['name'];
	                    $aco_node = $this->Acl->Aco->node($aco_path);
	                	
		                if(!empty($user_aro) && !empty($aco_node))
		                {
    	                    $action['user_permissions'] = array($user_id => $this->Acl->check($user, $aco_path) ? 1 : 0);
    	                }
    	                else
    	                {
    	                    $action['user_permissions'] = array($user_id => -1);
    	                }
	                }
	            }
	        }
	        
	        $this->set('user', $user);
	        $this->set('roles', $roles);
	        
	        $this->set('actions', $all_actions);
	    }
	}
	
	function admin_empty_permissions()
	{
	    if($this->Aro->Permission->deleteAll(array('Permission.id > ' => 0)))
	    {
	        $this->Session->setFlash(__d('acl', 'The permissions have been cleared', true), 'flash_message', null, 'plugin_acl');
	    }
	    else
	    {
	        $this->Session->setFlash(__d('acl', 'The permissions could not be cleared', true), 'flash_error', null, 'plugin_acl');
	    }
	    
	    $this->redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : array('action' => 'admin_index'));
	}
	
	
	function admin_grant_all_controllers($role_id)
	{
	    $role =& $this->{Configure :: read('acl.aro.role.model')};
        $role->id = $role_id;
        
		/*
         * Check if the Role exists in the ARO table
         */
        $node = $this->Acl->Aro->node($role);
        if(empty($node))
        {
            $asked_role = $role->read(null, $role_id);
            $this->Session->setFlash(sprintf(__d('acl', "The role '%s' does not exist in the ARO table", true), $asked_role['Role'][Configure :: read('acl.aro.role.display_field')]), 'flash_error');
        }
        else
        {
            //Allow to everything
            $this->Acl->allow($role, 'controllers');
        }
        
	    $this->_return_to_referer();
	}
	
	function admin_deny_all_controllers($role_id)
	{
	    $role =& $this->{Configure :: read('acl.aro.role.model')};
        $role->id = $role_id;
        
        /*
         * Check if the Role exists in the ARO table
         */
        $node = $this->Acl->Aro->node($role);
        if(empty($node))
        {
            $asked_role = $role->read(null, $role_id);
            $this->Session->setFlash(sprintf(__d('acl', "The role '%s' does not exist in the ARO table", true), $asked_role['Role'][Configure :: read('acl.aro.role.display_field')]), 'flash_error');
        }
        else
        {
            //Deny everything
            $this->Acl->deny($role, 'controllers');
        }
        
	    $this->_return_to_referer();
	}
	
	
	function admin_grant_role_permission($role_id)
	{
	    $role =& $this->{Configure :: read('acl.aro.role.model')};
        
        $role->id = $role_id;
        
        $aco_path = $this->_get_passed_aco_path();
        
        /*
         * Check if the role exists in the ARO table
         */
        $node = $this->Acl->Aro->node($role);
        if(!empty($node))
        {
            $create_new_permission = $this->_check_if_new_permission_needed($role, $aco_path, 'grant');
            
            if($create_new_permission)
            {
                if(!$this->Acl->allow($role, $aco_path))
                {
                    $this->set('acl_error', true);
                }
            }
        }
        else
        {
            $this->set('acl_error', true);
            $this->set('acl_error_aro', true);
        }
        
        $this->set('role_id', $role_id);
        $this->_set_aco_variables();
        
        if($this->RequestHandler->isAjax())
        {
            $this->render('ajax_role_granted');
        }
        else
        {
            $this->_return_to_referer();
        }
	}
	
	function admin_deny_role_permission($role_id)
	{
	    $role =& $this->{Configure :: read('acl.aro.role.model')};
        
        $role->id = $role_id;
        
        $aco_path = $this->_get_passed_aco_path();
        
        $create_new_permission = $this->_check_if_new_permission_needed($role, $aco_path, 'deny');
        
        if($create_new_permission)
        {
            if(!$this->Acl->deny($role, $aco_path))
            {
                 $this->set('acl_error', true);
            }
        }
        
        $this->set('role_id', $role_id);
        $this->_set_aco_variables();
        
        if($this->RequestHandler->isAjax())
        {
            $this->render('ajax_role_denied');
        }
        else
        {
            $this->_return_to_referer();
        }
	}
	
	
	function admin_grant_user_permission($user_id)
	{
	    $user =& $this->{Configure :: read('acl.aro.user.model')};
        
        $user->id = $user_id;

        $aco_path = $this->_get_passed_aco_path();
        
        /*
         * Check if the user exists in the ARO table
         */
        $aro_node = $this->Acl->Aro->node($user);
        if(!empty($aro_node))
        {
        	$aco_node = $this->Acl->Aco->node($aco_path);
        	
        	if(!empty($aco_node))
        	{
	            $create_new_permission = $this->_check_if_new_permission_needed($user, $aco_path, 'grant');
	            
	            if($create_new_permission)
	            {
	                if(!$this->Acl->allow($user, $aco_path))
	                {
	                    $this->set('acl_error', true);
	                }
	            }
        	}
        	else
        	{
        		$this->set('acl_error', true);
            	$this->set('acl_error_aco', true);
        	}
        }
        else
        {
            $this->set('acl_error', true);
            $this->set('acl_error_aro', true);
        }
        
        $this->set('user_id', $user_id);
        $this->_set_aco_variables();
        
        if($this->RequestHandler->isAjax())
        {
            $this->render('ajax_user_granted');
        }
        else
        {
            $this->_return_to_referer();
        }
	}
	
	function admin_deny_user_permission($user_id)
	{
	    $user =& $this->{Configure :: read('acl.aro.user.model')};
        
        $user->id = $user_id;

        $aco_path = $this->_get_passed_aco_path();
        
        /*
         * Check if the user exists in the ARO table
         */
        $aro_node = $this->Acl->Aro->node($user);
        if(!empty($aro_node))
        {
        	$aco_node = $this->Acl->Aco->node($aco_path);
        	
        	if(!empty($aco_node))
        	{
	            $create_new_permission = $this->_check_if_new_permission_needed($user, $aco_path, 'deny');
	            
	            if($create_new_permission)
	            {
	                if(!$this->Acl->deny($user, $aco_path))
	                {
	                    $this->set('acl_error', true);
	                }
	            }
        	}
        	else
        	{
        		$this->set('acl_error', true);
            	$this->set('acl_error_aco', true);
        	}
        }
        else
        {
            $this->set('acl_error', true);
            $this->set('acl_error_aro', true);
        }
        
        $this->set('user_id', $user_id);
        $this->_set_aco_variables();
        
        if($this->RequestHandler->isAjax())
        {
            $this->render('ajax_user_denied');
        }
        else
        {
            $this->_return_to_referer();
        }
	}
	

	/**
	 *
	 * @param $aro_model The model object that the Aro represent (e.g. A User object for an Aro with model = 'User' and foreign_key = 1)
	 * @param $aco_path The Aco path to check for
	 * @param $mode 'deny' or 'allow', 'grant', depending on what permission (grant or deny) is being set
	 */
	function _check_if_new_permission_needed($aro_model, $aco_path, $mode)
	{
	    $create_new_permission = true;
	    
	    $child_nodes = $this->Acl->Aco->node($aco_path);
	    
	    $parent_aco_path = 'controllers/' . $aco_path;
        while(strpos($parent_aco_path, '/') !== false)
        {
            $parent_aco_path = substr($parent_aco_path, 0, strrpos($parent_aco_path, '/'));
            
            if($mode == 'allow' || $mode == 'grant')
            {
                $check = $this->Acl->check($aro_model, $parent_aco_path);
            }
            else
            {
                $check = !$this->Acl->check($aro_model, $parent_aco_path);
            }
            
            if($check)
            {
                $parent_nodes = $this->Acl->Aco->node($parent_aco_path);
                
                /*
                 * All nodes that are in child_nodes but not in $nodes are useless
                 * to give the right access to the ACO to the ARO
                 */
                $child_aco_ids  = Set :: classicExtract($child_nodes,  '{n}.Aco.id');
                $parent_aco_ids = Set :: classicExtract($parent_nodes, '{n}.Aco.id');

                $aco_links_to_delete = array();
                foreach($child_aco_ids as $node_id)
                {
                    if(!in_array($node_id, $parent_aco_ids))
                    {
                        $aco_links_to_delete[] = $node_id;
                    }
                }

                if(count($aco_links_to_delete) > 0)
                {
                    $aro = $this->Acl->Aro->find('first', array('conditions' => array('model' => $aro_model->alias, 'foreign_key' => $aro_model->id)));
                    
                    if($this->Acl->Aro->Permission->deleteAll(array('aro_id' => $aro['Aro']['id'], 'aco_id' => $aco_links_to_delete)))
                    {
                        $create_new_permission = false;
                    }
                }
            }
        }
        
        return $create_new_permission;
	}
	
	
}
?>