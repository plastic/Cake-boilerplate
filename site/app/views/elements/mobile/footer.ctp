<?php
echo $html->scriptBlock('
	URL_BASE = "' . FULL_BASE_URL . $html->url('/') . '";
	SECTION = "' . (isset($this->section) ? $this->section : '') . '";
	SUBSECTION = "' . (isset($this->subSection) ? $this->subSection : '') . '";
	CURRENT_VIEW = "' . CURRENT_VIEW . '"
');

$jsDefault = array(
	'jquery-1.4.4.min',
	'mobile',
	'jquery.mobile-1.0a2.min'
	);

echo $html->script($jsDefault);
?>

<?php 
if (isset($this->requestJs)) :
	echo $html->script($this->requestJs);
endif;

if (file_exists(JS . CURRENT_VIEW . '.js'))
	echo $html->script(CURRENT_VIEW);
?>
