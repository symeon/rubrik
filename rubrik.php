<?php

$filename = $_GET['filename'];
$module = substr($filename, 0, -4);
$settings = parse_ini_file('rubriks/' . $filename, true);
$penalties = array(
    'None' => 0,
    'Other (5%)' => 5,
    '1 week (10%)' => 10,
    '2 weeks (15%)' => 15,
    '3 weeks (20%)' => 20,
    '4 weeks (25%)' => 25,
    '5 weeks (30%)' => 30,
    '6 weeks (35%)' => 35,
    '> 6 weeks (100%)' => 100,
);

// Form was submitted
$student_name = '';
$student_id = '';
$comments = '';
$msg = array();
$bg_indicators = false;
if (isset($_POST['student_name'])) {
    $student_name = $_POST['student_name'];
    $student_id = $_POST['student_id'];
    $penalty = $_POST['penalty'];
    $comments = $_POST['comments'];
	
	// Student name and ID are fine, proceed
	if ($student_name != '' && $student_id != '') {
		
		// Now check all grades
		$missing_variables = array();
		$mysql_table = '';
		$mysql_names = '';
		$mysql_values = '';
		$total = 0;
		$grade = 0;
		foreach ($settings as $setting_title => $setting_section) {
			foreach ($setting_section as $variable => $parameters) {
				$mysql_table .= ', ' . $variable.' DECIMAL(4,2) NOT NULL';
				$mysql_names .= ', `' . $variable.'`';
				if (!isset($_POST[$variable])) {
					$missing_variables[] = $variable;
				}
				else {
					$mysql_values .= ', ' . $_POST[$variable];
					$total += $_POST[$variable];
				}
			}
		}
		// All grades populated, proceed to processing
		if (count($missing_variables) == 0) {

		    $grade = round($total - ($penalty * $total / 100), 2);
		    
		    $settings = parse_ini_file('include/settings.ini');
		    $pdo = new PDO($settings['db_type'] . ':host=' . $settings['db_host'] . ';dbname=' . $settings['db_name'] . ';charset=' . $settings['db_charset'], $settings['db_username'], $settings['db_password']);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// Create the table if it doesn't exist yet
			$sql ='CREATE TABLE IF NOT EXISTS ' . $module . ' (
				id VARCHAR(9) PRIMARY KEY,
				name VARCHAR(50) NOT NULL,
				total DECIMAL(5,2) NOT NULL,
				penalty TINYINT(3) NOT NULL,
                grade DECIMAL(5,2) NOT NULL,
                comments TEXT NOT NULL'
				. $mysql_table . ')';
			$pdo->exec($sql);

			// Insert grades into the table
			$sql = $pdo->prepare('INSERT INTO ' . $module . ' (`id`, `name`, `total`, `penalty`, `grade`, `comments` ' . $mysql_names . ') 
				VALUES ( :id, :name, :total, :penalty, :grade, :comments' . $mysql_values . ')');
			$sql->bindValue(':id', $student_id);
			$sql->bindValue(':name', $student_name);
			$sql->bindValue(':total', $total);
			$sql->bindValue(':penalty', $penalty);
			$sql->bindValue(':grade', $grade);
			$sql->bindValue(':comments', $comments);
			$sql->execute();
			
			// Return to clean Rubrik page with success  message
			if ($grade == $total) {
			    $msg = 'Grade ' . $grade . ' successfully submitted for ' . $student_name;
			}
			else {
			    $msg = 'Grade ' . $grade . ' (' . $total . '-' . $penalty . '%) successfully submitted for ' . $student_name;
			}
			$args = array(
				'filename=' . $filename,
				'msg[type]=success',
			    'msg[text]=' . $msg,
			);
			$args = implode('&', $args);
			header("Location: " . basename($_SERVER['PHP_SELF']) . "?" . $args, true);
			exit();
		}
		// Some grades missing, return to form
		else {
			$missing_grades = '<br>';
			foreach ($missing_variables as $missing_variable) {
				$missing_grades .= '&bull; <a href="#' . $missing_variable . '">' . $missing_variable . '</a><br>';
			}
			$bg_indicators = true;
	        $msg['type'] = 'danger';
	        $msg['text'] = 'Some grades missing:' . $missing_grades . 'Please complete.';
		}
	}
	// Student information missing, return to form
	else {
        $msg['type'] = 'danger';
        $msg['text'] = 'Student information missing. Please complete.';
	}
}
// Clean form with success message
elseif (isset($_GET['msg'])) {
	$msg = $_GET['msg'];
}

$title = str_replace('_', ' ', $module) . ' Rubrik';
require_once 'include/header.php';
?>

        <div id="debug_link">
        	Running total:<br>
        	<span id="running_total">0</span>
        </div>

		<h1><?php echo str_replace('_', ' ', $module); ?> Rubrik</h1>
		
<?php
if (count($msg)) {
    ?>
		<p class="col-sm-offset-3 col-sm-6 text-center bg-<?php echo $msg['type']; ?>"><?php echo $msg['text']; ?></p>
<?php
}
?>
		<form method="post" action="rubrik.php?filename=<?php echo $filename; ?>" name="rubrik_form" id="rubrik_form" class="form-horizontal">
		
	    	<div class="form-group">
    			<label for="student_name" class="col-sm-4 control-label">Student name:</label>
    			<div class="col-sm-5">
    				<input type="text" class="form-control" id="student_name" name="student_name" value="<?php echo $student_name; ?>">
				</div>
    			<label for="student_id" class="col-sm-4 control-label">Student ID:</label>
    			<div class="col-sm-5">
    				<input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo $student_id; ?>">
				</div>
			</div>
		
<?php 
foreach ($settings as $setting_title => $setting_section) {
?>

			<fieldset class="rubrik">
				<legend class="rubrik"><?php echo $setting_title; ?></legend>
<?php
	foreach ($setting_section as $variable => $parameters) {
?>
	    		<div class="form-group">
<?php
		$previous_subsection = '';
		foreach ($parameters as $parameter_name => $parameter_value) {
			if ($parameter_name == 'text') {
				$bg_class = '';
				if ($bg_indicators && !isset($_POST[$variable])) {
					$bg_class = ' bg-danger';
				}
?>
	    			<label for="<?php echo $variable; ?>" id="<?php echo $variable; ?>" class="col-sm-4 control-label <?php echo $bg_class; ?>"><span data-tt="tooltip" data-placement="top" title="<?php echo $parameters['notes']; ?>"><?php echo $parameter_value; ?></span></label>
<?php 
			}
			elseif (substr($parameter_name, 0, 4) == 'text') {
				$index = substr($parameter_name, 4);
				$checked = '';
				if (isset($_POST[$variable]) && $_POST[$variable] == $parameters['value'.$index]) {
					$checked = ' checked';
				}
?>
	    				<label class="radio-inline"><input type="radio" name="<?php echo $variable; ?>" value="<?php echo $parameters['value'.$index]; ?>" <?php echo $checked; ?>> <span data-tt="tooltip" data-placement="top" title="<?php echo $parameters['value'.$index]; ?>"><?php echo $parameters['text'.$index]; ?></span> </label>
<?php 
			}
			else {
				continue;
			}
?>
<?php 
		}
?>
				</div><!-- form-group -->
<?php 
	}
?>
				
			</fieldset>
<?php 
}
?>

<?php 
?>
	    	<div class="form-group">
    			<label for="penalty" class="col-sm-4 control-label">Late submission penalty:</label>
    			<div class="col-sm-5">
    				<select id="penalty" name="penalty" class="form-control">
<?php
foreach ($penalties as $name => $value) {
    $selected = '';
    if (isset($penalty) && $penalty == $value) {
        $selected = ' selected';
    }
?>
		<option value="<?php echo $value; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
<?php
}
?>
    				</select>
				</div>
    			<label for="comments" class="col-sm-4 control-label">Comments:</label>
    			<div class="col-sm-5">
    				<textarea class="form-control" id="comments" name="comments"><?php echo $comments; ?></textarea>
				</div>
			</div>

		    <div class="form-group">
    			<div class="col-sm-offset-4 col-sm-4">
		        	<button type="submit" class="btn btn-primary btn-lg btn-block" id="rubrik_submit">Calculate grade</button>
		    	</div>
		    </div>

		</form>

		

		
<?php
require_once 'include/footer.php';
?>
