<?php
echo $html->scriptBlock('
	URL_BASE = "' . FULL_BASE_URL . $html->url('/') . '";
	SECTION = "' . (isset($this->section) ? $this->section : '') . '";
	SUBSECTION = "' . (isset($this->subSection) ? $this->subSection : '') . '";
	CURRENT_VIEW = "' . CURRENT_VIEW . '"
');
$jsDefault = array(
	'jquery-1.4.4.min',
	'plugins/ba-debug.min',
	'default'
);
if (!isset($this->requestJs)) :
	$this->requestJs = array();
endif;
?>
<?php #PNGS FIX IN IE ?>
<!--[if lt IE 7 ]>
	<?php echo $this->Html->script('plugins/dd_belatedpng'); ?>
	<script type="text/javascript" charset="utf-8">
		DD_belatedPNG.fix('img');
	</script>
<![endif]-->
<?php echo $this->ScriptCombiner->js(array_merge($jsDefault, $this->requestJs, array(CURRENT_VIEW))); ?>