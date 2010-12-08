<!doctype html>
<html lang="pt-br" class="no-js">
<head>
	<meta charset="utf-8">
	
	<title><?php echo isset($this->title) ? $this->title : 'PrudenShopping'; ?></title>
	
	<meta name="description" content="<?php echo isset($this->pageDescription) ? $this->pageDescription : ""; ?>" />
	<meta name="author" content="Mkt Virtual - Interactive Marketing" />
	<meta name="keywords" content="<?php echo isset($this->pageKeywords) ? $this->pageKeywords : ""; ?>" />
	
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
	<link rel="apple-touch-icon" href="apple-touch-icon.png" />
	
	<?php 
	echo $this->element('mobile/header');
	echo $this->element('mobile/footer');
	echo $scripts_for_layout;
	?>
</head>

<body <?php echo isset($this->jsFn) ? $this->jsFn : ""; ?>>

	<div id="<?php echo isset($this->section) ? $this->section : 'container'; ?>" data-role="page" data-theme="a">
		<div data-role="header">
			<h2><?php echo isset($this->mobileTitle) ? $this->mobileTitle : 'PrudenShopping' ?></h2>
		</div>
		<div id="main" data-role="content">
			<?php echo $content_for_layout ?>
		</div>
		<?php echo $this->element('mobile/nav'); ?>
	</div>
	
</body>
</html>