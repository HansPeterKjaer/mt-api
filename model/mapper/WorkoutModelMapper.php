<?php

class WorkoutModelMapper extends BaseModelMapper{
	
	public function fetchById(WorkoutModel &$model, $id){
		$dbh = $this->dbhandle;
		try{
			$stmt = $dbh->prepare("select * from workout where wo_id = :id");
			$stmt->execute(array(':id' => $id));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if (!empty($row)){
				$model->id = (int)$row['wo_id'];
				$model->name = $row['wo_name'];
				$model->diff = (int)$row['wo_diff'];
				$model->focus = (int)$row['wo_focus'];
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

	public function search(WorkoutListModel &$model, $itemsPerPage = 20, $page = 0, $term = '', $firstLetter = '', $sort = null, $diff = null, $focus = null, $time = null, $wId = null){
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
		if (!empty($time)){			$sqlCondition .= (($sqlCondition != '') ? 'AND ' : '') . 'wo_time < : :time '; }
		if ($sqlCondition != ''){   $sqlCondition =  'WHERE ' . $sqlCondition; };

		$sortStatement = 'ORDER BY wo_id DESC'; // default
		if (strtolower($sort) == 'date-asc'){ $sortStatement = 'ORDER BY wo_id ASC'; }
		if (strtolower($sort) == 'name-asc'){ $sortStatement = 'ORDER BY wo_name ASC'; }
		if (strtolower($sort) == 'name-desc'){ $sortStatement = 'ORDER BY wo_name DESC'; }
		if (strtolower($sort) == 'rand'){ $sortStatement = 'ORDER BY RAND()'; }
		if (strtolower($sort) == 'rand' && $wId){ $sortStatement = 'ORDER BY wo_id=:wId ASC, RAND()'; }
		
		$term = (!empty($term)) ? "%$term%" : '';
		$firstLetter = (!empty($firstLetter)) ? "$firstLetter%" : '';

		$sqlCount = "SELECT COUNT(*) FROM workout {$sqlCondition}";
		$sqlSelect = "SELECT wo_id AS id, wo_name AS name, wo_diff AS diff, wo_focus AS focus, wo_desc AS descr, wo_pr_id as prtc FROM workout {$sqlCondition} {$sortStatement} LIMIT :offset, :limit ";
		//echo $sqlSelect;

		try {
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
			if (!empty($wId))  			$stmt->bindParam(':wId', $wId);


			$stmt->execute();

			$model->workouts = $stmt->fetchAll(PDO::FETCH_CLASS, 'WorkoutModel');

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
			$msg = 'success';

		} catch (Exception $e) {
			if (@constant('DEVELOPMENT_ENVIRONMENT') == false)
				$msg = 'db error could not perform request';
			else
				echo ($e->xdebug_message);
				$msg = $e->xdebug_message;
		}
		return ['success' => $success, 'msg' => $msg];
	}

}

?>