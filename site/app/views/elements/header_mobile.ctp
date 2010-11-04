<?php
if ($this->params['action'] != 'display')
	define('CURRENT_VIEW', $this->params['controller'] . '/' . $this->params['action']);
else
	define('CURRENT_VIEW', $this->params['controller'] . '/' . $this->params['pass'][0]);

if (file_exists(WWW_ROOT . CSS_URL . CURRENT_VIEW . '.css')) 
	$assetCompress->css(CURRENT_VIEW);
	#echo $html->css(CURRENT_VIEW);
?>

<!-- <link rel="stylesheet" href="<?php echo CSS_URL . CURRENT_VIEW . '.css' ?>" type="text/css" /> -->

<?php
#echo $html->css('default');
$assetCompress->css('mobile');
$assetCompress->css('jquery.mobile-1.0a1.min');

if (isset($this->requestCss)) :
	foreach ($this->requestCss as $jsPath) :
		$assetCompress->css($jsPath);
	endforeach;
endif;

if (isset($this->setMeta))
{
	foreach ($this->setMeta as $meta) 
	{
		echo '<meta name="' . $meta['name'] . '" content="' . $meta['content'] . '" />';
	}
}