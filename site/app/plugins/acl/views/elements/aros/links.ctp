<div id="aros_link" class="acl_links">
<?php 
$selected = isset($selected) ? $selected : $this->params['action'];

$links = array();
$links[] = $html->link(__d('acl', 'Build missing AROs', true), '/admin/acl/aros/check', array('class' => ($selected == 'admin_check' )? 'selected' : null));
$links[] = $html->link(__d('acl', 'Users roles', true), '/admin/acl/aros/users', array('class' => ($selected == 'admin_users' )? 'selected' : null));
$links[] = $html->link(__d('acl', 'Roles permissions', true), '/admin/acl/aros/role_permissions', array('class' => ($selected == 'admin_role_permissions' )? 'selected' : null));
$links[] = $html->link(__d('acl', 'Users permissions', true), '/admin/acl/aros/user_permissions', array('class' => ($selected == 'admin_user_permissions' )? 'selected' : null));

echo $html->nestedList($links, array('class' => 'acl_links'));
?>
</div>