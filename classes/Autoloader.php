<?php

 /* MLAPHP #1: Defining Autoloader */
class Autoloader
{
	// Register with SPL on object instantiation
	public function __construct() {
		spl_autoload_register(array($this, 'load'));
	}
	
	// Application logic for autoloading classes
	private function load($class)
	{
		// Strip off any leading namespace separators
		$class = ltrim($class, '\\');
		
		// The eventual file pathinfo
		$subpath = '';
		
		// Is there a namespace separator?
		$pos = strpos($class, '\\');
		if ($pos !== false) {
			// Convert namespace characters to directory separators
			$ns = substr($class, 0, $pos);
			$subpath = str_replace('\\', DIRECTORY_SEPARATOR, $ns) . DIRECTORY_SEPARATOR;
			// Remove the namespace portion from the final class name portion
			$class = substr($class, $pos + 1);
		}
		
		// Convert underscores in the class name to directory separators
		$subpath .= str_replace('_', DIRECTORY_SEPARATOR, $class);
		
		// The path to the class directory location
		$dir = __DIR__;
		
		// Construct the directory location and require iterator_apply
		$file = $dir . DIRECTORY_SEPARATOR . $subpath . '.php';
		require $file;
	}
}
