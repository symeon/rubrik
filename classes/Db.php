<?php

class Db
{
	private $db;
	private $student_name;
	private $student_id;
	private $module;
	private $credentials = array();
	
	public function __construct(Settings $settings)
	{
		$this->db = new PDO(
			$settings->get('db_type') . 
			':host=' . $settings->get('db_host') . 
			';dbname=' . $settings->get('db_name') . 
			';charset=' . $settings->get('db_charset'), 
			$settings->get('db_username'), 
			$settings->get('db_password')
		);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$this->student_name = trim($_POST['student_name']);
		$this->student_id = trim($_POST['student_id']);
		$this->module = $_POST['module'];
	}
	
	public function fetch_record()
	{
		$where = array();
		
		if ($this->student_name != '') {
			$where[] = 'name = :name';
			$this->credentials[] = 'student ' . $this->student_name;
		}
		if ($this->student_id != '') {
			$where[] = 'id = :id';
			$this->credentials[] = 'ID ' . $this->student_id;
		}
		$where = implode(' AND ', $where);
		$this->credentials = implode(' with ', $this->credentials);
		
		// Fetch record from the appropriate table
		$sql = $this->db->prepare('SELECT * FROM ' . $this->module . ' WHERE ' . $where);
		if ($this->student_name != '') {
			$sql->bindValue(':name', $this->student_name);
		}
		if ($this->student_id != '') {
			$sql->bindValue(':id', $this->student_id);
		}
		$sql->execute();

		$result = $sql->fetchAll(\PDO::FETCH_ASSOC);
		return $result;
	}
	
	public function getCredentials()
	{
		return $this->credentials;
	}
	
}