<?php

class Settings
{
	private $settings;
	
	public function __construct($file, $flag = false)
	{
		$this->settings = parse_ini_file($file, $flag);
	}
	
	public function get($var = null)
	{
		if (isset($var)) {
			return $this->settings[$var];
		} else {
			return $this->settings;
		}
	}
}