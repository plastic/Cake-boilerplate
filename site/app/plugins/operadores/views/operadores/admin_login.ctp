<?php $this->requestCss[] = "admin_form"; ?>

<h2>Acesso ao Sistema</h2>

<?php echo $form->create('Operador', array('url' => array('controller' => 'operadores', 'action' => 'login'), 'class' => 'loginController')); ?>

	<fieldset>
		<legend>Login</legend>
		<?php echo $form->input('email', array('label' => 'Login', 'size' => '40')); ?>
		<?php echo $form->input('senha', array('type' => 'password', 'label' => 'Senha', 'size' => '40' )); ?>
	</fieldset>
	
	<div class="submit actions">
		<?php echo $form->submit('Login', array('class' => 'salvar', 'div' => false)); ?>
	</div>

	
<?php echo $form->end(); ?>