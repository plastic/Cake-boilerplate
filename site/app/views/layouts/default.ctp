<!doctype html>
<html lang="pt-br" class="no-js">
<head>
  <meta charset="utf-8">

	<!--[if IE]>
		<?php echo $this->Html->script('html5/html5'); ?>
	<![endif]-->

	<title><?php echo isset($this->title) ? $this->title : ""; ?></title>
	
	<meta name="description" content="<?php echo isset($this->pageDescription) ? $this->pageDescription : ""; ?>" />
	<meta name="keywords" content="<?php echo isset($this->pageKeywords) ? $this->pageKeywords : ""; ?>" />
	<meta name="author" content="Mkt Virtual - Interactive Marketing" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<?php echo $this->element('header'); ?>
	
</head>

<body>

	<div id="container">
		<header></header>
		
		<div id="main">
			<?php echo $content_for_layout ?>
		</div>
		
		<footer></footer>
	</div>

<?php 
echo $this->element('footer');
echo $this->element('sql_dump');
echo $scripts_for_layout;
?>
</body>
</html>