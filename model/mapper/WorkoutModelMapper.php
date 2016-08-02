<?php

class WorkoutModelMapper extends BaseModelMapper{
	public function insert($name, $diff, $focus, $descr, $protocol, $exercises = []){
    	$dbh = $this->dbhandle;
    	$status = false;
    	$msg = "";

    	$dbh->beginTransaction();
    	try {
			$stmt = $dbh->prepare("INSERT INTO workout (wo_name, wo_diff, wo_focus, wo_desc, wo_pr_id) VALUES (:name, :diff, :focus, :descr, :protocol)");
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':diff', $diff);
	        $stmt->bindParam(':focus', $focus);
	        $stmt->bindParam(':descr', $descr);
	        $stmt->bindParam(':protocol', $protocol);
	        $stmt->execute();
	        
	        $id = $dbh->lastInsertId();
	        $stmt = $dbh->prepare("INSERT INTO workout_exercise (ex_id, wo_id, ex_index) VALUES (:ex_id, {$id}, :ex_index )");
			$count = 0;
			
			foreach ($exercises as $ex) {
				$c = $count++;
				$stmt->bindParam(':ex_id', $ex);
				$stmt->bindParam(':ex_index', $c, PDO::PARAM_INT);
	        	$stmt->execute();
	        }	        

	        $dbh->commit();
	        $msg = "Exercise successfully created";
	        $status = true;

	    } catch (Exception $e) {
	    	$dbh->rollback();
			if (@constant('DEVELOPMENT_ENVIRONMENT') == false)
				$msg = "Db error could not perform request";
			else
				$msg = $e->xdebug_message;
		}
		return ['status' => $status, 'msg' => $msg];
    }

    public function update($id, $name, $diff, $focus, $descr, $protocol, $exercises = []){
    	$dbh = $this->dbhandle;
    	$status = false;
    	$msg = "";

    	$dbh->beginTransaction();
    	try {
    		$stmt = $dbh->prepare("UPDATE workout SET wo_name = :name, wo_diff = :diff, wo_focus = :focus, wo_desc = :descr, wo_pr_id = :protocol WHERE wo_id = :id");
			$stmt->bindParam(':id', $id);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':diff', $diff);
	        $stmt->bindParam(':focus', $focus);
	        $stmt->bindParam(':descr', $descr);
	        $stmt->bindParam(':protocol', $protocol);
	        $result = $stmt->execute();

	        if($result != false){
	        	$stmt = $dbh->prepare("DELETE FROM workout_exercise WHERE wo_id = :id");
	        	$stmt->bindParam(':id', $id);
		        $stmt->execute();

		        $count = 0;
		        $stmt = $dbh->prepare("INSERT INTO workout_exercise (ex_id, wo_id, ex_index) VALUES (:ex_id, :wo_id, :ex_index)");
		        $stmt->bindParam(':wo_id', $id);
				
				foreach ($exercises as $ex_id) {
					$c = $count++;
					$stmt->bindParam(':ex_id', $ex_id, PDO::PARAM_INT);
					$stmt->bindParam(':ex_index', $c, PDO::PARAM_INT);
		        	$stmt->execute();
		        }	
	        }
	        
	        $dbh->commit();
	        $msg = "Exercise successfully updated";
	        $status = true;

		}catch (Exception $e) {
			$dbh->rollback();
			if (@constant('DEVELOPMENT_ENVIRONMENT') == false)
				$msg = "db error could not perform request";
			else
				$msg = $e->xdebug_message;
		}
		return ['status' => $status, 'msg' => $msg];
    } 

    /*public function fetch(WorkoutModel &$model){
    	$dbh = $this->dbhandle;
    	$stmt = $dbh->prepare("select * from workout where wo_id = :id");
		$stmt->execute(array(':id' => $model->id));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if (!empty($row)){
    		$model->id = $row['wo_id'];
    		$model->name = $row['wo_name'];
			$model->diff = $row['wo_diff'];
			$model->focus = $row['wo_focus'];
			$model->descr = $row['wo_desc'];
		}
    }*/
    public function fetchById(WorkoutModel &$model, $id){
    	$dbh = $this->dbhandle;
    	try{
	    	$stmt = $dbh->prepare("select * from workout where wo_id = :id");
			$stmt->execute(array(':id' => $id));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if (!empty($row)){
				//$model = new WorkoutModel();
	    		$model->id = $row['wo_id'];
	    		$model->name = $row['wo_name'];
				$model->diff = $row['wo_diff'];
				$model->focus = $row['wo_focus'];
				$model->descr = $row['wo_desc'];
				$model->protocol = new ProtocolModel();

				$pr_id = $row['wo_pr_id'];

				$modelFactory = new ModelFactory($this->dbhandle);
				$mapper = $modelFactory->buildMapper('ExerciseModelMapper');
				$mapper->fetchByWorkoutId($model->exercises, $model->id);

				$mapper = $modelFactory->buildMapper('ProtocolModelMapper');
				$mapper->fetchById($model->protocol, $pr_id);

				return true;
			}
			else
			{
				return false;
			}
		}
		catch (PDOException $e) {
			Logger::log($e);
		    return false;
		}
    }

    /*public function fetchAll(WorkoutListModel &$model){
    	return $this->search($model, 0);
	}*/

	public function search(WorkoutListModel &$model, $itemsPerPage = 20, $page = 0, $term = '', $firstLetter = '', $sort = null, $diff = null, $focus = null){
    	$dbh = $this->dbhandle;
    	$success = false;
    	$msg = "";

		$page = $page; 
		$offset = ($page > 0) ? ($page - 1) * $itemsPerPage : 0;
		$limit = ($itemsPerPage == 0) ? 999999 : $offset + $itemsPerPage; // if itemsperpage == 0 -> take all 

		$sqlCondition = '';
		
		if (!empty($term)){ 		$sqlCondition .= (($sqlCondition != '') ? 'AND ' : '') . 'wo_name LIKE :term '; }
		if (!empty($firstLetter)){ 	$sqlCondition .= (($sqlCondition != '') ? 'AND ' : '') . 'wo_name LIKE :firstLetter '; }
		if (!empty($diff)){ 		$sqlCondition .= (($sqlCondition != '') ? 'AND ' : '') . 'wo_diff = :diff '; }
		if (!empty($focus)){ 		$sqlCondition .= (($sqlCondition != '') ? 'AND ' : '') . 'wo_focus = :focus '; }
		if ($sqlCondition != ''){   $sqlCondition =  'WHERE ' . $sqlCondition; };

		$sortStatement = 'ORDER BY wo_id DESC'; // default
		if (strtolower($sort) == 'date-asc'){ $sortStatement = 'ORDER BY wo_id ASC'; }
		if (strtolower($sort) == 'name-asc'){ $sortStatement = 'ORDER BY wo_name ASC'; }
		if (strtolower($sort) == 'name-desc'){ $sortStatement = 'ORDER BY wo_name DESC'; }
		if (strtolower($sort) == 'rand'){ $sortStatement = 'ORDER BY RAND()'; }
		
		$term = (!empty($term)) ? "%$term%" : $term;
		$firstLetter = (!empty($firstLetter)) ? "$firstLetter%" : $firstLetter;

		$sqlCount = "SELECT COUNT(*) FROM workout {$sqlCondition}";
		$sqlSelect = "SELECT wo_id AS id, wo_name AS name, wo_diff AS diff, wo_focus AS focus, wo_desc AS descr, wo_pr_id as prtc FROM workout {$sqlCondition} {$sortStatement} LIMIT :offset, :limit ";
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

	        // get workouts
			$stmt = $dbh->prepare($sqlSelect);
			$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
			$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
			
			if (!empty($term))  		$stmt->bindParam(':term', $term);
			if (!empty($firstLetter))  	$stmt->bindParam(':firstLetter', $firstLetter); 
			if (!empty($diff))  		$stmt->bindParam(':diff', $diff); 
			if (!empty($focus))  		$stmt->bindParam(':focus', $focus);

			$stmt->execute();

			$model->workouts = $stmt->fetchAll(PDO::FETCH_CLASS, "WorkoutModel");

			// fetch exercises and protocols:
			$modelFactory = new ModelFactory($this->dbhandle);
			$exerciseMapper = $modelFactory->buildMapper('ExerciseModelMapper');
			$protocolMapper = $modelFactory->buildMapper('ProtocolModelMapper');

			foreach ($model->workouts as $wo){
				$exerciseMapper->fetchByWorkoutId($wo->exercises, $wo->id);
				$protocolMapper->fetchById($wo->protocol, $wo->prtc);
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

/*	public function fetchBySearchData(Array &$model, $term, $diff, $focus){
    	$dbh = $this->dbhandle;
    	$success = false;
    	$msg = "";

    	try {
    		if(empty($term) && empty($diff) && empty($focus)) throw new Exception('Error: No search data provided');

	    	$sql = "SELECT 
	    			wo_id, 
	    			wo_name, 
	    			wo_diff, 
	    			wo_focus, 
	    			wo_desc, 
	    			wo_pr_id,
	    			pr_id,
	    			pr_name,
	    			pr_desc FROM workout JOIN protocol ON workout.wo_pr_id = protocol.pr_id ";
	    	
	    	if (!empty($term) && !empty($diff) && !empty($focus)) $sql .= "WHERE wo_name LIKE :term AND wo_diff = :diff AND wo_focus = :focus";
	    	else if (!empty($term) && !empty($focus)) $sql .= " WHERE wo_name LIKE :term AND  wo_focus = :focus";
	    	else if (!empty($term) && !empty($diff)) $sql .= " WHERE wo_name LIKE :term AND wo_diff = :diff";
	    	else if (!empty($focus) && !empty($diff)) $sql .= " WHERE wo_focus LIKE :focus' AND wo_diff = :diff";
	    	else if (!empty($term)) $sql .= " WHERE wo_name LIKE :term";
			else if (!empty($diff)) $sql .= " WHERE wo_diff = :diff";
	    	else if (!empty($focus)) $sql .= " WHERE wo_focus = :focus";
	    	
	    	$stmt = $dbh->prepare($sql);
	    	if (!empty($term)) $stmt->bindValue(':term', '%' . $term .'%');
			if (!empty($diff)) $stmt->bindParam(':diff', $diff);
		    if (!empty($focus)) $stmt->bindParam(':focus', $focus);

			$stmt->execute();

			while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
			   $workout = new WorkoutModel();
			   $workout->id = $row['wo_id'];
			   $workout->name = $row['wo_name'];
			   $workout->diff = $row['wo_diff'];
			   $workout->focus = $row['wo_focus'];
			   $workout->descr = $row['wo_desc'];
			   $protocol = new ProtocolModel();
			   $protocol->id = $row['pr_id'];
			   $protocol->name = $row['pr_name'];
			   $protocol->descr = $row['pr_desc'];
			   $workout->prtc = $protocol;

			   $stmt2 = $dbh->prepare("SELECT workout_exercise.ex_id AS id , ex_name AS name, ex_diff AS diff, ex_focus AS focus, ex_desc AS descr, ex_img AS img FROM exercise, workout_exercise WHERE workout_exercise.wo_id = :wo_id AND exercise.ex_id = workout_exercise.ex_id");
			   $stmt2->bindParam(':wo_id', $row['wo_id']);
			   $stmt2->execute();
			   $exercises = $stmt2->fetchAll(PDO::FETCH_CLASS, "exerciseModel");
			   $workout->exercises = $exercises;
			   $model[] = $workout;
			}
			//$model = $stmt->fetchAll(PDO::FETCH_CLASS, "WorkoutModel");
			$msg = "fetched " . count($model) . " rows";
			$success = true;

		} catch (Exception $e) {
			if (@constant('DEVELOPMENT_ENVIRONMENT') == false)
				$msg = "db error could not perform request";
			else
				$msg = $e->xdebug_message;
		}
		return ['success' => $success, 'msg' => $msg];
    }*/
    public function delete($id){
    	$dbh = $this->dbhandle;
    	
    	$stmt = $dbh->prepare("DELETE FROM workout_exercise WHERE wo_id = :id");
    	$stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
		$stmt->execute();

    	$stmt = $dbh->prepare("DELETE FROM workout WHERE wo_id = :id");
    	$stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
		$count = $stmt->execute();
		
		return $count;
    }
}

?>