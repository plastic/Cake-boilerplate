<?php 
echo $html->css('/acl/css/acl.css');
?>
<div id="plugin_acl">
	
	<?php 
	echo $this->Session->flash('plugin_acl');
	?>
	
	<h1><?php echo __d('acl', 'ACL plugin', true); ?></h1>
	
	<?php

	if(!isset($no_acl_links))
	{
	    $selected = isset($selected) ? $selected : $this->params['controller'];
    
        $links = array();
        $links[] = $html->link(__d('acl', 'Permissions', true), '/admin/acl/aros/index', array('class' => ($selected == 'aros' )? 'selected' : null));
        $links[] = $html->link(__d('acl', 'Actions', true), '/admin/acl/acos/index', array('class' => ($selected == 'acos' )? 'selected' : null));
        
        echo $html->nestedList($links, array('class' => 'acl_links'));
	}
	?>