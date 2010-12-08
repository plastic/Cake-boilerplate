<?php
if ($this->params['action'] != 'display')
	define('CURRENT_VIEW', $this->params['controller'] . '/' . $this->params['action']);
else
	define('CURRENT_VIEW', $this->params['controller'] . '/' . $this->params['pass'][0]);

if (!isset($this->requestCss)) :
	$this->requestCss = array();
endif;

if (isset($this->setMeta))
{
	foreach ($this->setMeta as $meta) 
	{
		echo '<meta name="' . $meta['name'] . '" content="' . $meta['content'] . '" />';
	}
}

echo $this->ScriptCombiner->css(array_merge(array('default'), $this->requestCss, array(CURRENT_VIEW)));
?>