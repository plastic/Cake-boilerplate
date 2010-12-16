<?php 
if ( $this->action == "admin_adicionar") 
	$url = $action;
else
	$url = 'editar/' . $this->params['pass'][0];
?>

<?php echo $form->create('Operador', array('url' => $url)); ?>
	<?php echo $form->hidden('id'); ?>
	<?php echo $form->input('nome', array('label' => 'Nome')); ?>
	<?php echo $form->input('email', array('label' => 'E-mail')); ?>
	<?php echo $form->input('senha', array('type' => 'password', 'label' => 'Senha')); ?>
	<?php echo $form->input('confirm', array('type' => 'password', 'label' => 'Confirme')); ?>
	
	<p class="submit actions">
		<?php echo $form->submit('Salvar', array('class' => 'salvar', 'div' => false)); ?>
		ou
		<?php echo $html->link('Voltar', '/admin/operadores/operadores', array('class' => 'cancelar')); ?>
	</p>
	
<?php echo $form->end(); ?>