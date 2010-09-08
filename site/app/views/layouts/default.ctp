<!doctype html>
<html lang="pt-br" class="no-js">
<head>
  <meta charset="utf-8">

	<!--[if IE]>
		<?php echo $this->Html->script('html5/html5'); ?>
	<![endif]-->

	<title><?php echo isset($this->title) ? $this->title : ""; ?></title>
	
	<meta name="description" content="<?php echo isset($this->pageDescription) ? $this->pageDescription : ""; ?>" />
	<meta name="author" content="Mkt Virtual - Interactive Marketing" />
	<meta name="keywords" content="<?php echo isset($this->pageKeywords) ? $this->pageKeywords : ""; ?>" />
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link rel="apple-touch-icon" href="apple-touch-icon.png" />
	
	<?php echo $this->element('header'); ?>
</head>

<!--[if lt IE 7 ]> <body class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <body class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <body class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <body class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <body> <!--<![endif]-->

	<div id="container">
		<header></header>
		
		<div id="main">
			<?php echo $content_for_layout ?>
		</div>
		
		<footer></footer>
	</div> <!-- end of #container -->

<?php 
echo $this->element('footer');
echo $assetCompress->includeAssets();
echo $scripts_for_layout;
?>
</body>
</html>