<?php
echo $html->scriptBlock('
	URL_BASE = "' . FULL_BASE_URL . $html->url('/') . '";
	SECTION = "' . (isset($this->section) ? $this->section : '') . '";
	SUBSECTION = "' . (isset($this->subSection) ? $this->subSection : '') . '";
	CURRENT_VIEW = "' . CURRENT_VIEW . '"
');

$jsDefault = array(
	'jquery-1.4.3.min',
	'jquery.mobile-1.0a1.min',
	'mobile'
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
