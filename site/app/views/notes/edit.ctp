<div class="notes form">
<?php echo $this->Form->create('Note');?>
	<fieldset>
 		<legend><?php __('Edit Note'); ?></legend>
	<?php
		echo $this->Form->input('id');
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

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Note.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Note.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Notes', true), array('action' => 'index'));?></li>
	</ul>
</div>