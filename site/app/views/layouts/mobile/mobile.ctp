<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN"
        "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	
	<title><?php echo isset($this->title) ? $this->title : ""; ?></title>
	<meta http-equiv="content-type"  content="application/xhtml+xml; charset=utf-8" /> 
	<meta name="description" content="<?php echo isset($this->pageDescription) ? $this->pageDescription : ""; ?>" />
	<meta name="author" content="Mkt Virtual - Interactive Marketing" />
	<meta name="keywords" content="<?php echo isset($this->pageKeywords) ? $this->pageKeywords : ""; ?>" />
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0" />
	<link rel="apple-touch-icon" href="apple-touch-icon.png" />
	<?php echo $this->element('header_mobile'); ?>
	
</head>

<body>
	
	<h1>Titulo da Página</h1>
	<h2>Mobile text</h2>
	<p>Welcome to the first template of this book</p>
	<p>It <strong>should work</strong> in every mobile browser in the market</p>
	
	<div><?php echo $content_for_layout ?></div>
		
	<ol>
		<li><a href="http://m.yahoo.com" accesskey="1">Yahoo!</a></li>
		<li><a href="http://m.google.com" accesskey="2">Google</a></li>
		<li><a href="http://m.bing.com" accesskey="3">Bing</a></li>
	</ol>
	
	<p><img src="images/copyright.gif" width="150" height="50" alt="(C) mobilexweb.com" /></p>
	
	<?php echo $this->element('footer_mobile'); ?>
	<?php echo $scripts_for_layout; ?>
	
</body>
</html>