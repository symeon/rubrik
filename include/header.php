<?php
$rubrik_directory = 'rubriks/';
if (isset($_GET['dir']) && $_GET['dir'] != '') {
    $rubrik_directory .= '/' . $_GET['dir'];
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Symeon Charalabides (cosmopolite trainee)">
    
    <link rel="stylesheet" type="text/css" media="screen" href="//<?php echo $_SERVER['HTTP_HOST']; ?>/rubrik/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="//<?php echo $_SERVER['HTTP_HOST']; ?>/rubrik/css/rubrik.css" />
    
    <script type="text/javascript" charset="utf-8" src="//<?php echo $_SERVER['HTTP_HOST']; ?>/rubrik/js/jquery.js"></script>
    <script type="text/javascript" charset="utf-8" src="//<?php echo $_SERVER['HTTP_HOST']; ?>/rubrik/js/bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="//<?php echo $_SERVER['HTTP_HOST']; ?>/rubrik/js/rubrik.js"></script>
  </head>
  <body>
  
    <div class="container">

    	<div id="header">
			<a href="index.php">Search Grade</a><br><br>
<?php 
$p1 = opendir($rubrik_directory);
$i = 0;
$tables = array();
while ($rubrik_filename = readdir($p1)) {
	if (is_dir($rubrik_directory.$rubrik_filename) && $rubrik_filename == 'old') {
?>
			<a href="rubrik.php?dir=<?php echo $rubrik_filename; ?>">[<?php echo $rubrik_filename; ?>]</a><br>
<?php
	}
	if (!is_dir($rubrik_directory.$rubrik_filename) && substr($rubrik_filename, -3) == 'ini') {
	$name = substr($rubrik_filename, 0, -4);
	$tables[] = $name;
?>
			<a href="rubrik.php?filename=<?php echo $rubrik_filename; ?>"><?php echo str_replace('_', ' ', $name); ?></a><br>
<?php
	}
}
?>
		</div>
