<?php
echo $html->scriptBlock('
	URL_BASE = "' . FULL_BASE_URL . $html->url('/') . '";
	SECTION = "' . (isset($this->section) ? $this->section : '') . '";
	SUBSECTION = "' . (isset($this->subSection) ? $this->subSection : '') . '";
	CURRENT_VIEW = "' . CURRENT_VIEW . '"
');

$jsDefault = array(
	'jquery-1.4.2.min',
	'plugins/ba-debug.min',
	'swfobject/swfobject',
	'plugins/jquery.easing.1.3.js',
	'default'
	);

#echo $html->script($jsDefault);
foreach($jsDefault as $js)
	$assetCompress->script($js);
?>

<?php 
if (isset($this->requestJs)) :
	foreach ($this->requestJs as $jsPath) :
		$assetCompress->script($jsPath);
	endforeach;
endif;
#echo $html->script($this->requestJs);

if (file_exists(JS . CURRENT_VIEW . '.js'))
	$assetCompress->script(CURRENT_VIEW);
#echo $html->script(CURRENT_VIEW);
?>

<?php #PNGS FIX IN IE ?>
<!--[if lt IE 7 ]>
	<?php echo $this->Html->script('plugins/dd_belatedpng'); ?>
	<script type="text/javascript" charset="utf-8">
		DD_belatedPNG.fix('img');
	</script>
<![endif]-->