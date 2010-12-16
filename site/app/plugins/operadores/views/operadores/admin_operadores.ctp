<p class="actions">
	<?php echo $html->link('Adicionar operador', array('controller' => 'operadores', 'action' => 'adicionar', 'admin' => true), array('class' => 'adicionar')); ?>
</p>

<table cellpadding="0" cellspacing="0" class="dados">
	<thead>
		<tr>
			<th>Nome</th>
			<th>E-mail</th>
			<th>Ações</th>
		</tr>
	</thead>
	
	<tbody>
		<?php if ( !empty($operadores) ) : ?>
			<?php foreach($operadores as $key => $operador) : ?>
				<tr class="grid-item strip-<?php echo $key % 2; ?>">
					<td><?php echo $operador['Operador']['nome']; ?></td>
					<td><?php echo $operador['Operador']['email']; ?></td>
					<td class="actions">
						<?php echo $html->link('Alterar', '/admin/operadores/editar/' . $operador['Operador']['id'], array('class' => 'editar')); ?>
						<?php echo $html->link('Excluir', '/admin/operadores/excluir/' . $operador['Operador']['id'], array('class' => 'excluir')); ?>
					</td>
				</tr>
			<?php endforeach ?>
		<?php else : ?>
			<tr>
				<td colspan="3" align="center"><p class="warning">Sem operadores no momento!</p></td>
			</tr>
		<?php endif ?>
	</tbody>
</table>