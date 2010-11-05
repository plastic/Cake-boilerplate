<?php 
echo $this->Html->script('/acl/js/jquery');
echo $this->Html->script('/acl/js/acl_plugin');

echo $this->element('design/header');
?>

<?php 
echo $this->element('aros/links');
?>

<?php 
if(isset($users))
{
?>
	<?php 
	echo '<p>&nbsp;</p>';
	echo '<p>';
	echo __d('acl', 'This page allows to manage users specific rights', true);
	echo '</p>';
	echo '<p>&nbsp;</p>';
	?>
    <table border="0" cellpadding="5" cellspacing="2">
    <tr>
    	<?php 
    	$column_count = 1;
    	
    	$headers = array($paginator->sort(__d('acl', 'user', true), 'display_name'));
    	
    	echo $html->tableHeaders($headers);
    	?>
    </tr>
    <?php 
    foreach($users as $user)
    {
        echo '<tr>';
        echo '  <td>' . $user[$user_model_name]['display_name'] . '</td>';
        $title = __d('acl', 'Manage user specific rights', true);
        echo '  <td>' . $html->link($html->image('/acl/img/design/lock.png'), '/admin/acl/aros/user_permissions/' . $user[$user_model_name]['id'], array('alt' => $title, 'title' => $title, 'escape' => false)) . '</td>';
        echo '</tr>';
    }
    ?>
    <tr>
    	<td class="paging" colspan="<?php echo $column_count ?>">
    		<?php echo $paginator->numbers(); ?>
    	</td>
    </tr>
    </table>
<?php 
}
else
{
?>
    <h1><?php echo  __d('acl', $user_model_name, true) . ' : ' . $user[$user_model_name]['display_name']; ?></h1>
    
    <h2><?php echo __d('acl', 'Role', true); ?></h2>
    
    <table border="0">
    <tr>
    	<?php 
    	foreach($roles as $role)
    	{
    	    echo '<td>';
    	    
    	    echo $role[$role_model_name][$role_display_field];
    	    if($role[$role_model_name]['id'] == $user[$user_model_name][$role_fk_name])
    	    {
    	        echo $html->image('/acl/img/design/tick.png');
    	    }
    	    else
    	    {
    	    	$title = __d('acl', 'Update the user role', true);
    	        echo $html->link($html->image('/acl/img/design/tick_disabled.png'), array('plugin' => 'acl', 'controller' => 'aros', 'action' => 'update_user_role', 'user' => $user[$user_model_name]['id'], 'role' => $role[$role_model_name]['id']), array('title' => $title, 'alt' => $title, 'escape' => false));
    	    }
    	    
    	    echo '</td>';
    	}
    	?>
    </tr>
    </table>
    
    <h2><?php echo __d('acl', 'Permissions', true); ?></h2>
    
    <table border="0" cellpadding="5" cellspacing="2">
	<tr>
    	<?php
    	
    	$column_count = 1;
    	
    	$headers = array(__d('acl', 'action', true), __d('acl', 'authorization', true));

    	echo $html->tableHeaders($headers);
    	?>
	</tr>
	
	<?php
	$previous_ctrl_name = '';
	
	foreach($actions['app'] as $controller_name => $ctrl_infos)
	{
		if($previous_ctrl_name != $controller_name)
		{
			$previous_ctrl_name = $controller_name;
			
			$color = (isset($color) && $color == 'color1') ? 'color2' : 'color1';
		}
		
		foreach($ctrl_infos as $ctrl_info)
		{
    		echo '<tr class="' . $color . '">
    		';
    		
    		echo '<td>' . $controller_name . '->' . $ctrl_info['name'] . '</td>';
    		
	    	echo '<td>';
	    	echo '<span id="right__' . $user[$user_model_name]['id'] . '_' . $controller_name . '_' . $ctrl_info['name'] . '">';
        			
    		if($ctrl_info['user_permissions'][$user[$user_model_name]['id']] == 1)
    		{
    		    $js->buffer('register_user_toggle_right(true, "' . $html->url('/') . '", "right__' . $user[$user_model_name]['id'] . '_' . $controller_name . '_' . $ctrl_info['name'] . '", "' . $user[$user_model_name]['id'] . '", "", "' . $controller_name . '", "' . $ctrl_info['name'] . '")');
    		    
    		    echo $html->image('/acl/img/design/tick.png', array('class' => 'pointer'));
    		}
    		elseif($ctrl_info['user_permissions'][$user[$user_model_name]['id']] == 0)
    		{
    		    $js->buffer('register_user_toggle_right(false, "' . $html->url('/') . '", "right__' . $user[$user_model_name]['id'] . '_' . $controller_name . '_' . $ctrl_info['name'] . '", "' . $user[$user_model_name]['id'] . '", "", "' . $controller_name . '", "' . $ctrl_info['name'] . '")');
    		    
    			echo $html->image('/acl/img/design/cross.png', array('class' => 'pointer'));
    		}
    		elseif($ctrl_info['user_permissions'][$user[$user_model_name]['id']] == -1)
    		{
    		    echo $html->image('/acl/img/design/important16.png');
    		}
    		
	    	echo '</span>';
	    	
	    	echo ' ';
	    	echo $html->image('/acl/img/ajax/waiting12.gif', array('id' => 'right__' . $user[$user_model_name]['id'] . '_' . $controller_name . '_' . $ctrl_info['name'] . '_spinner', 'style' => 'display:none;'));
    		
	    	echo '</td>';
	    	echo '</tr>
	    	';
		}    		
	}
	?>
	<?php 
    	foreach($actions['plugin'] as $plugin_name => $plugin_ctrler_infos)
    	{
    	    echo '<tr class="title"><td colspan="2">' . __d('acl', 'Plugin', true) . ' ' . $plugin_name . '</td></tr>
    	    ';
    	    
    	    foreach($plugin_ctrler_infos as $plugin_ctrler_name => $plugin_methods)
    	    {
        	    if($previous_ctrl_name != $plugin_ctrler_name)
        		{
        			$previous_ctrl_name = $plugin_ctrler_name;
        			
        			$color = (isset($color) && $color == 'color1') ? 'color2' : 'color1';
        		}
    			
    	        foreach($plugin_methods as $method)
    	        {
    	            echo '<tr class="' . $color . '">
    	            ';
    	            
    	            echo '<td>' . $plugin_ctrler_name . '->' . $method['name'] . '</td>';
    	            //debug($method['name']);
    	            
    	            echo '<td>';
    	            echo '<span id="right_' . $plugin_name . '_' . $user[$user_model_name]['id'] . '_' . $plugin_ctrler_name . '_' . $method['name'] . '">';
    	            
		    		if($method['user_permissions'][$user[$user_model_name]['id']] == 1)
		    		{
		    		    $js->buffer('register_user_toggle_right(true, "' . $html->url('/') . '", "right_' . $plugin_name . '_' . $user[$user_model_name]['id'] . '_' . $plugin_ctrler_name . '_' . $method['name'] . '", "' . $user[$user_model_name]['id'] . '", "' . $plugin_name . '", "' . $plugin_ctrler_name . '", "' . $method['name'] . '")');
		    		    
		    			echo $html->image('/acl/img/design/tick.png', array('class' => 'pointer'));
		    		}
		    		elseif($method['user_permissions'][$user[$user_model_name]['id']] == 0)
		    		{
		    		    $js->buffer('register_user_toggle_right(false, "' . $html->url('/') . '", "right_' . $plugin_name . '_' . $user[$user_model_name]['id'] . '_' . $plugin_ctrler_name . '_' . $method['name'] . '", "' . $user[$user_model_name]['id'] . '", "' . $plugin_name . '", "' . $plugin_ctrler_name . '", "' . $method['name'] . '")');
		    			
		    		    echo $html->image('/acl/img/design/cross.png', array('class' => 'pointer'));
		    		}
		    		elseif($method['user_permissions'][$user[$user_model_name]['id']] == -1)
            		{
            		    echo $html->image('/acl/img/design/important16.png');
            		}
            		else
            		{
            		    echo '?';
            		}
            		
    		    	echo '</span>';
    		    	
    		    	echo ' ';
    		    	echo $html->image('/acl/img/ajax/waiting12.gif', array('id' => 'right_' . $plugin_name . '_' . $user[$user_model_name]['id'] . '_' . $plugin_ctrler_name . '_' . $method['name'] . '_spinner', 'style' => 'display:none;'));
    		    	
        		    echo '</td>';
    	            echo '</tr>
    	            ';
    	        }
    	    }
    	}
    	?>
	</table>
    <?php 
    echo $html->image('/acl/img/design/tick.png') . ' ' . __d('acl', 'authorized', true);
    echo '&nbsp;&nbsp;&nbsp;';
    echo $html->image('/acl/img/design/cross.png') . ' ' . __d('acl', 'blocked', true);
    ?>
<?php    
}
?>
<?php
echo $this->element('design/footer');
?>