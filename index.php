<?php
/*
  TODO:
  Different colours for different subsections
  Datasets 1-2 side by side?
 */

$student_name = '';
$student_id = '';
$msg = array();
if (isset($_POST['module']) && $_POST['module'] != '') {
	$module = $_POST['module'];
	$student_name = $_POST['student_name'];
	$student_id = $_POST['student_id'];
	
	// Student name and ID are fine, proceed
	if ($student_name != '' || $student_id != '') {
	    $settings = parse_ini_file('include/settings.ini');
	    $pdo = new PDO($settings['db_type'] . ':host=' . $settings['db_host'] . ';dbname=' . $settings['db_name'] . ';charset=' . $settings['db_charset'], $settings['db_username'], $settings['db_password']);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$where = array();
		$credentials = array();
		if ($student_name != '') {
			$where[] = 'name = :name';
			$credentials[] = 'student ' . $student_name;
		}
		if ($student_id != '') {
			$where[] = 'id = :id';
			$credentials[] = 'ID ' . $student_id;
		}
		$where = implode(' AND ', $where);
		$credentials = implode(' with ', $credentials);
		
		// Insert grades into the table
		$sql = $pdo->prepare('SELECT * FROM ' . $module . ' WHERE ' . $where);
		if ($student_name != '') {
			$sql->bindValue(':name', $student_name);
		}
		if ($student_id != '') {
			$sql->bindValue(':id', $student_id);
		}
		$sql->execute();

		$result = $sql->fetchAll(\PDO::FETCH_ASSOC);

		// Positive result: display as JSON
		if (count($result) > 0) {
			$json = print_r(json_encode($result, JSON_PRETTY_PRINT), 1);
	        $msg['type'] = 'success';
	        $msg['text'] = '<strong>Results for ' . $credentials . ' retrieved: </strong><br>' . $json;
		}
		// No results
		else {
	        $msg['type'] = 'danger';
	        $msg['text'] = 'No results for ' . $credentials . ' found.';
		}
		
#		print '<pre>';print_r($result);print '</pre>';
	}
	// Student information missing, return to form
	else {
        $msg['type'] = 'danger';
        $msg['text'] = 'Student information missing. Please complete.';
	}
	
}

$title = 'Search Student Grade';
require_once 'include/header.php';
?>

		<h1>Search Student Grade</h1>
		
<?php
if (count($msg)) {
?>
		<p class="col-sm-offset-3 col-sm-6 text-center bg-<?php echo $msg['type']; ?>"><?php echo $msg['text']; ?></p>
<?php
}
?>
		<form method="post" action="index.php" name="search_form" id="search_form" class="form-horizontal">
		
	    	<div class="form-group">
    			<label for="module" class="col-sm-4 control-label">Module:</label>
    			<div class="col-sm-5">
    				<select id="module" name="module" class="form-control">
    					<option value="">Choose...</option>
<?php
foreach ($tables as $name) {
    $selected = '';
    if (isset($module) && $module == $name) {
        $selected = ' selected';
    }
?>
		<option value="<?php echo $name; ?>"<?php echo $selected; ?>><?php echo str_replace('_', ' ', $name); ?></option>
<?php
}
?>
    				</select>
				</div>
			</div>

	    	<div class="form-group">
    			<label for="student_name" class="col-sm-4 control-label">Student name:</label>
    			<div class="col-sm-5">
    				<input type="text" class="form-control" id="student_name" name="student_name" value="<?php echo $student_name; ?>">
				</div>
			</div>
	    	<div class="form-group">
    			<label for="student_id" class="col-sm-4 control-label">Student ID:</label>
    			<div class="col-sm-5">
    				<input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo $student_id; ?>">
				</div>
			</div>
		
		    <div class="form-group">
    			<div class="col-sm-offset-4 col-sm-4">
		        	<button type="submit" class="btn btn-primary btn-lg btn-block" id="search_submit">Search grade</button>
		    	</div>
		    </div>

		</form>
		
<?php
require_once 'include/footer.php';
?>
