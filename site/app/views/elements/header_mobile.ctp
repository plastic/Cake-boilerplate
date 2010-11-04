<?php
if ($this->params['action'] != 'display')
	define('CURRENT_VIEW', $this->params['controller'] . '/' . $this->params['action']);
else
	define('CURRENT_VIEW', $this->params['controller'] . '/' . $this->params['pass'][0]);

if (file_exists(WWW_ROOT . CSS_URL . CURRENT_VIEW . '.css')) 
	echo $html->css(CURRENT_VIEW);
	
echo $html->css('jquery.mobile-1.0a1', null, array('media' => 'screen'));
echo $html->css('handheld', null, array('media' => 'handheld'));

if (isset($this->requestCss)) :
	echo $html->css($this->requestCss);
endif;

if (isset($this->setMeta))
{
	foreach ($this->setMeta as $meta) 
	{
		echo '<meta name="' . $meta['name'] . '" content="' . $meta['content'] . '" />';
	}
}