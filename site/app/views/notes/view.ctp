<div class="notes view">
<h2><?php  __('Note');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $note['Note']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Title'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $note['Note']['title']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Body'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $note['Note']['body']; ?>
			&nbsp;
		</dd>
	</dl>
	
	<?php /*
	<ul id="tagcloud">
		<?php 
			echo $this->TagCloud->display($tags, array(
				'before' => '<li size="%size%" class="tag">',
				'after' => '</li>',
				'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'view'),
				'named' => false
			));
		?>
		</ul>
	*/ ?>
	
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Note', true), array('action' => 'edit', $note['Note']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Note', true), array('action' => 'delete', $note['Note']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $note['Note']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Notes', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Note', true), array('action' => 'add')); ?> </li>
	</ul>
</div>
