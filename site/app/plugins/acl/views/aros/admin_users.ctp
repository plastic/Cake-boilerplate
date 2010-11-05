<?php 
echo $this->element('design/header');
?>

<?php 
echo $this->element('aros/links');
?>

<table border="0" cellpadding="5" cellspacing="2">
<tr>
	<?php 
	$column_count = 1;
	
	$headers = array($paginator->sort(__d('acl', 'name', true), 'display_name'));
	
	foreach($roles as $role)
	{
	    $headers[] = $role[$role_model_name][$role_display_field];
	    $column_count++;
	}
	
	echo $html->tableHeaders($headers);
	
	?>
	
</tr>
<?php 
foreach($users as $user)
{
    $style = isset($user['Aro']) ? '' : ' class="line_warning"';
    
    echo '<tr' . $style . '>';
    echo '  <td>' . $user[$user_model_name]['display_name'] . '</td>';
    
    foreach($roles as $role)
	{
	   if(isset($user['Aro']) && $role[$role_model_name]['id'] == $user[$user_model_name][$role_fk_name])
	   {
	       echo '  <td>' . $html->image('/acl/img/design/tick.png') . '</td>';
	   }
	   else
	   {
	   	   $title = __d('acl', 'Update the user role', true);
	       echo '  <td>' . $html->link($html->image('/acl/img/design/tick_disabled.png'), '/admin/acl/aros/update_user_role/user:' . $user[$user_model_name]['id'] . '/role:' . $role[$role_model_name]['id'], array('title' => $title, 'alt' => $title, 'escape' => false)) . '</td>';
	   }
	}
	
    //echo '  <td>' . (isset($user['Aro']) ? $html->image('/acl/img/design/tick.png') : $html->image('/acl/img/design/cross.png')) . '</td>';
    
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
if($missing_aro)
{
?>
    <div style="margin-top:20px">
    
    <p class="warning"><?php echo __d('acl', 'Some users AROS are missing. Click on a role to assign one to a user.', true) ?></p>
    
    <?php 
    //echo $html->link(___('generate missing AROs', true), array('plugin' => 'acl', 'controller' => 'aros', 'action' => 'generate_missing'));
    ?>
    
    </div>

<?php    
}
?>

<?php
echo $this->element('design/footer');
?>