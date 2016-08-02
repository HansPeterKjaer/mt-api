<?php

class ProtocolModelMapper extends BaseModelMapper{
	
	public function insert(ProtocolModel &$model){
    	$dbh = $this->dbhandle;
    	try {
			$stmt = $dbh->prepare("INSERT INTO protocol (pr_name, pr_desc) VALUES (:name, :descr)");
			$stmt->bindParam(':name', $model->name);
	        $stmt->bindParam(':descr', $model->descr);
	        $stmt->execute();
	        
	        $model->id = $dbh->lastInsertId();
	        return array('msg'=>'Protocol added', 'status'=>true);
	    } catch (PDOException $e) {
		    return array('msg'=>'An error occured - Please try again later', 'status'=>false);
		} 
    }

    public function update(ProtocolModel &$model){
    	$dbh = $this->dbhandle;
    	try {
			$stmt = $dbh->prepare("UPDATE protocol SET pr_name=:name, pr_desc=:descr WHERE pr_id = :id ");
			$stmt->bindParam(':id', $model->id, PDO::PARAM_INT);
			$stmt->bindParam(':name', $model->name);
	        $stmt->bindParam(':descr', $model->descr);
	        $stmt->execute();

	        return true;
	    } catch (PDOException $e) {
	    	Logger::log($e);
	    	exit();
		    return false;
		} 		
    } 

    public function delete($id){
    	$dbh = $this->dbhandle;
    	$stmt = $dbh->prepare("DELETE FROM protocol WHERE pr_id = :id");
    	$stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
		$count = $stmt->execute();
		return $count;
    }

    public function search(ProtocolListModel &$model, $itemsPerPage = 20, $page = 0, $term = '', $firstLetter = '', $sort = null){
    	$dbh = $this->dbhandle;
    	$success = false;
    	$msg = "";

		$page = $page; 
		$offset = ($page > 0) ? ($page - 1) * $itemsPerPage : 0;
		$limit = ($itemsPerPage == 0) ? 999999 : $offset + $itemsPerPage; // if itemsperpage == 0 -> take all 

		$sqlCondition = '';
		
		if (!empty($term)){ 		$sqlCondition .= (($sqlCondition != '') ? 'AND ' : '') . 'pr_name LIKE :term '; }
		if (!empty($firstLetter)){ 	$sqlCondition .= (($sqlCondition != '') ? 'AND ' : '') . 'pr_name LIKE :firstLetter '; }
		if ($sqlCondition != ''){   $sqlCondition =  'WHERE ' . $sqlCondition; };
		
		$sortStatement = 'ORDER BY pr_id DESC'; // default
		if (strtolower($sort) == 'date asc'){ $sortStatement = 'ORDER BY pr_id ASC'; }
		if (strtolower($sort) == 'name asc'){ $sortStatement = 'ORDER BY pr_name ASC'; }
		if (strtolower($sort) == 'name desc'){ $sortStatement = 'ORDER BY pr_name DESC'; }

		$term = (!empty($term)) ? "%$term%" : $term;
		$firstLetter = (!empty($firstLetter)) ? "$firstLetter%" : $firstLetter;

		$sqlCount = "SELECT COUNT(*) FROM protocol {$sqlCondition}";
		$sqlSelect = "SELECT pr_id AS id, pr_name AS name, pr_desc AS descr FROM protocol {$sqlCondition} {$sortStatement} LIMIT :offset, :limit ";
		//echo $sqlSelect;

    	try {
    		$stmt = $dbh->prepare($sqlCount);	
			
			if (!empty($term))  		$stmt->bindParam(':term', $term);
			if (!empty($firstLetter))  	$stmt->bindParam(':firstLetter', $firstLetter); 

			$stmt->execute();
	        $rows = $stmt->fetchColumn(); 

	        // get exercises
			$stmt = $dbh->prepare($sqlSelect);
			$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
			$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
			
			if (!empty($term))  		$stmt->bindParam(':term', $term);
			if (!empty($firstLetter))  	$stmt->bindParam(':firstLetter', $firstLetter); 

			$stmt->execute();

			$model->protocols = $stmt->fetchAll(PDO::FETCH_CLASS, "ProtocolModel");

			// set listmodel data:
			$model->currentPage = $page;
			$model->totalPages = ($itemsPerPage == 0) ? 1 : (int)ceil($rows / $itemsPerPage);

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

    public function fetchById(ProtocolModel &$model, $id){
    	$dbh = $this->dbhandle;
    	$stmt = $dbh->prepare("select * from protocol where pr_id = :id");
		$stmt->execute(array(':id' => $id));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!empty($row)){
    		$model->id = $row['pr_id'];
    		$model->name = $row['pr_name'];
			$model->descr = $row['pr_desc'];
		}
    }

	public function fetchAll(ProtocolListModel &$model){
		return $this->search($model, 0);
    }
}

?>