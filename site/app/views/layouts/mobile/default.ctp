<!doctype html>
<html lang="pt-br" class="no-js">
<head>
	<meta charset="utf-8">
	
	<title><?php echo isset($this->title) ? $this->title : ""; ?></title>
	
	<meta name="description" content="<?php echo isset($this->pageDescription) ? $this->pageDescription : ""; ?>" />
	<meta name="author" content="Mkt Virtual - Interactive Marketing" />
	<meta name="keywords" content="<?php echo isset($this->pageKeywords) ? $this->pageKeywords : ""; ?>" />
	
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
	<link rel="apple-touch-icon" href="apple-touch-icon.png" />
	
	<?php echo $this->element('header_mobile'); ?>
</head>

<body>

	<div id="container" data-role="page" data-theme="b">
		
		<div data-role="header">
			<h2>CakePHP - Painel de Controle</h2>
		</div>
		
		<div id="main" data-role="content">
			<?php echo $content_for_layout ?>
		</div>
		
		<div data-role="footer">
			<h3>MKT Virtual</h3>
		</div>
	</div>
	
<?php 
echo $this->element('footer_mobile');
echo $assetCompress->includeAssets();
echo $scripts_for_layout;
?>
</body>
</html>