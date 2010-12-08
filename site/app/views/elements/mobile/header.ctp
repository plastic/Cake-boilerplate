<?php
if ($this->params['action'] != 'display')
	define('CURRENT_VIEW', $this->params['controller'] . '/' . $this->params['action']);
else
	define('CURRENT_VIEW', $this->params['controller'] . '/' . $this->params['pass'][0]);

if (file_exists(WWW_ROOT . CSS_URL . CURRENT_VIEW . '.css')) 
	echo $this->Html->css(CURRENT_VIEW);

echo $this->Html->css('mobile', null, array('media' => 'screen'));
echo $this->Html->css('jquery.mobile-1.0a2', null, array('media' => 'screen'));
echo $this->Html->css('handheld', null, array('media' => 'handheld'));

if (isset($this->requestCss)) :
	echo $this->Html->css($this->requestCss);
endif;

if (isset($this->setMeta))
{
	foreach ($this->setMeta as $meta) 
	{
		echo '<meta name="' . $meta['name'] . '" content="' . $meta['content'] . '" />';
	}
}