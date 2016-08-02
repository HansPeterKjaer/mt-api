<?php

class ExerciseModelMapper extends BaseModelMapper{
	public function insert(ExerciseModel &$model){
    	$dbh = $this->dbhandle;

    	try {
			$stmt = $dbh->prepare("INSERT INTO exercise (ex_name, ex_diff, ex_focus, ex_desc) VALUES (:name, :diff, :focus, :descr)");
			$stmt->bindParam(':name', $model->name);
			$stmt->bindParam(':diff', $model->diff);
	        $stmt->bindParam(':focus', $model->focus);
	        $stmt->bindParam(':descr', $model->descr);
	        $stmt->execute();

	        $id = $dbh->lastInsertId();
	        $count = 0;

			$stmt = $dbh->prepare("INSERT INTO exercise_image (ex_id, img_id, img_index) VALUES ($id, :img_id, :imageIndex)");
	        foreach ($model->images as $value) {
	        	$c = $count++;
	        	$stmt->bindParam(':img_id', $value);
	        	$stmt->bindParam(':imageIndex', $c);
	        	$stmt->execute();
	        }
	        
	        return true;
	    } catch (PDOException $e){
	    	Logger::log($e);
	    	return false;
	    }	    
    }

    public function update(ExerciseModel &$model){
    	$dbh = $this->dbhandle;
    	try {
    		$stmt = $dbh->prepare("UPDATE exercise SET ex_name = :name, ex_diff = :diff, ex_focus = :focus, ex_desc = :descr WHERE ex_id = :id");
			$stmt->bindParam(':id', $model->id);
			$stmt->bindParam(':name', $model->name);
			$stmt->bindParam(':diff', $model->diff);
	        $stmt->bindParam(':focus', $model->focus);
	        $stmt->bindParam(':descr', $model->descr);
	        $stmt->execute();

	        $count = 0;

	        $stmt = $dbh->prepare("DELETE FROM exercise_image WHERE ex_id = :id");
	        $stmt->bindParam(':id', $model->id);
	        $stmt->execute();

	        $stmt = $dbh->prepare("INSERT INTO exercise_image (ex_id, img_id, img_index) VALUES (:id, :img_id, :imageIndex)");
	        foreach ($model->images as $value) {
	        	$c = $count++;
	        	$stmt->bindParam(':id', $model->id);
	        	$stmt->bindParam(':img_id', $value);
	        	$stmt->bindParam(':imageIndex', $c);
	        	$stmt->execute();
	        }
	        return true;
		} catch (PDOException $e) {
			Logger::log($e);
		    return false;
		}	
    } 

    public function fetchById(&$model, $id){
    	$dbh = $this->dbhandle;

    	$stmt = $dbh->prepare("select * from exercise where ex_id = :id");
		$stmt->execute(array(':id' => $id));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if (!empty($row)){
			$exercise = new ExerciseModel();
    		$exercise->id = $row['ex_id'];
    		$exercise->name = $row['ex_name'];
			$exercise->diff = $row['ex_diff'];
			$exercise->focus = $row['ex_focus'];
			$exercise->descr = $row['ex_desc'];

			//$stmt = $dbh->prepare("SELECT img_name FROM exercise_image, image WHERE ex_id = $exercise->id AND exercise_image.img_id = image.img_id  ORDER BY img_index");
			//$stmt->execute();
			//$exercise->images = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
			$modelFactory = new ModelFactory($this->dbhandle);
			$mapper = $modelFactory->buildMapper('MediaModelMapper');
			$mapper->fetchExerciseImages($exercise->images, $exercise->id);
			
			$model = $exercise; // why array
			return true; 
		}
		else {
			return false;
		}
    }

    public function fetchByName(&$model, $name){
    	$dbh = $this->dbhandle;

    	$stmt = $dbh->prepare("select * from exercise where ex_name = :name");
		$stmt->execute(array(':name' => $name));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if (!empty($row)){
			$exercise = new ExerciseModel();
    		$exercise->id = $row['ex_id'];
    		$exercise->name = $row['ex_name'];
			$exercise->diff = $row['ex_diff'];
			$exercise->focus = $row['ex_focus'];
			$exercise->descr = $row['ex_desc'];

			$modelFactory = new ModelFactory($this->dbhandle);
			$mapper = $modelFactory->buildMapper('MediaModelMapper');
			$mapper->fetchExerciseImages($exercise->images, $exercise->id);
			
			$model = $exercise;
			return true; 
		}
		else {
			return false;
		}
    }

    public function fetchByWorkoutId(Array &$exercises, $woId){
    	$dbh = $this->dbhandle;

    	try{
	    	$stmt = $dbh->prepare("select exercise.ex_id AS id, ex_name AS name, ex_diff AS diff, ex_focus AS focus, ex_desc AS descr, ex_img as img from exercise, workout_exercise WHERE wo_id = :wo_id AND exercise.ex_id = workout_exercise.ex_id ORDER BY workout_exercise.ex_index ");
			$stmt->execute(array(':wo_id' => $woId));
			$exercises = $stmt->fetchAll(PDO::FETCH_CLASS, "ExerciseModel");

			foreach ($exercises as $ex) {
				$modelFactory = new ModelFactory($this->dbhandle);
				$mapper = $modelFactory->buildMapper('MediaModelMapper');
				$mapper->fetchExerciseImages($ex->images, $ex->id);
			}
			return true; 
		}
		catch (PDOException $e) {
			Logger::log($e);
		    return false;
		}
    }

    public function search(exerciseListModel &$model, $itemsPerPage = 20, $page = 0, $term = '', $firstLetter = '', $sort = null, $diff = null, $focus = null){
    	$dbh = $this->dbhandle;
    	$success = false;
    	$msg = "";

		$page = $page; 
		$offset = ($page > 0) ? ($page - 1) * $itemsPerPage : 0;
		$limit = ($itemsPerPage == 0) ? 999999 : $offset + $itemsPerPage; // if itemsperpage == 0 -> take all 

		$sqlCondition = '';
		
		if (!empty($term)){ 		$sqlCondition .= (($sqlCondition != '') ? 'AND ' : '') . 'ex_name LIKE :term '; }
		if (!empty($firstLetter)){ 	$sqlCondition .= (($sqlCondition != '') ? 'AND ' : '') . 'ex_name LIKE :firstLetter '; }
		if (!empty($diff)){ 		$sqlCondition .= (($sqlCondition != '') ? 'AND ' : '') . 'ex_diff = :diff '; }
		if (!empty($focus)){ 		$sqlCondition .= (($sqlCondition != '') ? 'AND ' : '') . 'ex_focus = :focus '; }
		if ($sqlCondition != ''){   $sqlCondition =  'WHERE ' . $sqlCondition; };
		
		$sortStatement = 'ORDER BY ex_id DESC'; // default
		if (strtolower($sort) == 'date-asc'){ $sortStatement = 'ORDER BY ex_id ASC'; }
		if (strtolower($sort) == 'name-asc'){ $sortStatement = 'ORDER BY ex_name ASC'; }
		if (strtolower($sort) == 'name-desc'){ $sortStatement = 'ORDER BY ex_name DESC'; }

		$term = (!empty($term)) ? "%$term%" : $term;
		$firstLetter = (!empty($firstLetter)) ? "$firstLetter%" : $firstLetter;

		$sqlCount = "SELECT COUNT(*) FROM exercise {$sqlCondition}";
		$sqlSelect = "SELECT ex_id AS id, ex_name AS name, ex_diff AS diff, ex_focus AS focus, ex_desc AS descr, ex_img as img FROM exercise {$sqlCondition} {$sortStatement} LIMIT :offset, :limit ";
		//echo $sqlSelect;

    	try {
    		// get count;
    		$stmt = $dbh->prepare($sqlCount);	
			
			if (!empty($term))  		$stmt->bindParam(':term', $term);
			if (!empty($firstLetter))  	$stmt->bindParam(':firstLetter', $firstLetter); 
			if (!empty($diff))  		$stmt->bindParam(':diff', $diff); 
			if (!empty($focus))  		$stmt->bindParam(':focus', $focus);

			$stmt->execute();
	        $rows = $stmt->fetchColumn(); 

	        // get exercises
			$stmt = $dbh->prepare($sqlSelect);
			$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
			$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
			
			if (!empty($term))  		$stmt->bindParam(':term', $term);
			if (!empty($firstLetter))  	$stmt->bindParam(':firstLetter', $firstLetter); 
			if (!empty($diff))  		$stmt->bindParam(':diff', $diff); 
			if (!empty($focus))  		$stmt->bindParam(':focus', $focus);

			$stmt->execute();

			$model->exercises = $stmt->fetchAll(PDO::FETCH_CLASS, "ExerciseModel");

			// fetch exercises and protocols:
			//$modelFactory = new ModelFactory($this->dbhandle);
			//$exerciseMapper = $modelFactory->buildMapper('ExerciseModelMapper');
			//$protocolMapper = $modelFactory->buildMapper('ProtocolModelMapper');
			$modelFactory = new ModelFactory($this->dbhandle);
			$mapper = $modelFactory->buildMapper('MediaModelMapper');
			
			foreach ($model->exercises as $ex){
				$mapper->fetchExerciseImages($ex->images, $ex->id);
			}

			// set listmodel data:
			$model->currentPage = $page;
			$model->totalPages = (int)ceil($rows / $itemsPerPage);

			$success = true;
			$msg = "success";

		} catch (Exception $e) {
			if (@constant('DEVELOPMENT_ENVIRONMENT') == false)
				$msg = "db error could not perform request";
			else
				echo ($e->xdebug_message);
				$msg = $e->xdebug_message;
		}
		return ['success' => $success, 'msg' => $msg];
	}

    public function fetchBySearchData(Array &$model, $term, $diff, $focus){
    	$dbh = $this->dbhandle;
    	$success = false;
    	$msg = "";

    	try {
    		if(empty($term) && empty($diff) && empty($focus)) throw new Exception('Error: No search data provided');

	    	$sql = "SELECT ex_id AS id, ex_name AS name, ex_diff AS diff, ex_focus AS focus, ex_desc AS descr, ex_img as img FROM exercise";
	    	
	    	if (!empty($term) && !empty($diff) && !empty($focus)) $sql .= "WHERE ex_name LIKE :term AND ex_diff = :diff AND ex_focus = :focus";
	    	else if (!empty($term) && !empty($focus)) $sql .= " WHERE ex_name LIKE :term AND  ex_focus = :focus";
	    	else if (!empty($term) && !empty($diff)) $sql .= " WHERE ex_name LIKE :term AND ex_diff = :diff";
	    	else if (!empty($focus) && !empty($diff)) $sql .= " WHERE ex_focus LIKE :focus' AND ex_diff = :diff";
	    	else if (!empty($term)) $sql .= " WHERE ex_name LIKE :term";
			else if (!empty($diff)) $sql .= " WHERE ex_diff = :diff";
	    	else if (!empty($focus)) $sql .= " WHERE ex_focus = :focus";
	    	
	    	$stmt = $dbh->prepare($sql);
	    	if (!empty($term)) $stmt->bindValue(':term', '%' . $term .'%');
			if (!empty($diff)) $stmt->bindParam(':diff', $diff);
		    if (!empty($focus)) $stmt->bindParam(':focus', $focus);
			$stmt->execute();

			$model = $stmt->fetchAll(PDO::FETCH_CLASS, "ExerciseModel");
			$msg = "fetched " . count($model) . " rows";
			$success = true;

		} catch (Exception $e) {
			if (@constant('DEVELOPMENT_ENVIRONMENT') == false)
				$msg = "db error could not perform request";
			else
				$msg = $e->xdebug_message;
		}
		return ['success' => $success, 'msg' => $msg];
    }

    public function fetchAll(Array &$array, $images = true){
    	$dbh = $this->dbhandle;
    	$stmt = $dbh->prepare("SELECT ex_id AS id, ex_name AS name, ex_diff AS diff, ex_focus AS focus, ex_desc AS descr, ex_img as img FROM exercise");
		$stmt->execute();
		$array = $stmt->fetchAll(PDO::FETCH_CLASS, "ExerciseModel");

		if($images){
			foreach ($array as $ex){
				$stmt = $dbh->prepare("SELECT img_name, image.img_id FROM exercise_image, image WHERE ex_id = $ex->id AND exercise_image.img_id = image.img_id ORDER BY img_index");
				$stmt->execute();
				$ex->images = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
			}	
		}
    }
/*
    public function fetchRange(Array &$array, $limit = 10, $offset = 0){
    	$dbh = $this->dbhandle;
    	$stmt = $dbh->prepare("SELECT ex_id AS id, ex_name AS name, ex_diff AS diff, ex_focus AS focus, ex_desc AS descr, ex_img as img FROM exercise ORDER BY ex_id DESC LIMIT :offset, :limit ");
    	$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
		$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
		$stmt->execute();
		$array = $stmt->fetchAll(PDO::FETCH_CLASS, "ExerciseModel");
    }
*/    
    public function delete($id){
    	$dbh = $this->dbhandle;
    	$success = false;
    	$msg = '';
    	try {
	    	$stmt = $dbh->prepare("DELETE FROM exercise WHERE ex_id = :id");
	    	$stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
			$count = $stmt->execute();
			if ($count > 0 ) {
				$success = true;
				$msg = 'Image deleted succesfully';
			}
		} catch (Exception $e) {
			if ($e->getCode() == '23000') {
				$msg = 'Error: exercise is used in active workouts';
			}
			else if (@constant('DEVELOPMENT_ENVIRONMENT') == false) {
				$msg = 'db error could not perform request';
			}
			else {
				$msg = json_encode($e->xdebug_message);		
			}
		}

		return ['success' => $success, 'msg' => $msg];
    }
}

?>