<div class="notes form">
<?php echo $this->Form->create('Note');?>
	<fieldset>
 		<legend><?php __('Add Note'); ?></legend>
	<?php
		echo $this->Form->input('title');
		echo $this->Form->input('body');
		echo $this->Form->input('tags', array('type' => 'text'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Notes', true), array('action' => 'index'));?></li>
	</ul>
</div>