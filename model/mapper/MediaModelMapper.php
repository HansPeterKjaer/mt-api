<?php 

class MediaModelMapper extends BaseModelMapper{
	public function fetchByImageName(MediaListModel &$mediaList, $imageName){
		$dbh = $this->dbhandle;
		try {
			$stmt = $dbh->prepare('SELECT img_id as id, img_name as imageName FROM image WHERE img_name = :imageName');
			
			$stmt->bindParam(':imageName', $imageName);
	        $stmt->execute();

	        $mediaList->items = $stmt->fetchAll(PDO::FETCH_CLASS, 'MediaModel');
	        return true;
	    } 
	    catch (PDOException $e) {
	    	Logger::log($e);
		    return false;
		}
	}

	public function fetchExerciseImages(MediaListModel &$mediaList, $exId){
		$dbh = $this->dbhandle;
		try {
			$stmt = $dbh->prepare('SELECT image.img_id as id, img_name as imageName FROM image, exercise_image WHERE image.img_id = exercise_image.img_id AND ex_id = :exId ORDER BY img_index');
			
			$stmt->bindParam(':exId', $exId);
	        $stmt->execute();

	        $mediaList->items = $stmt->fetchAll(PDO::FETCH_CLASS, 'MediaModel');
	        return true;
	    } 
	    catch (PDOException $e) {
	    	Logger::log($e);
		    return false;
		}
	}

	public function search(MediaListModel &$mediaList, $term, $firstLetter = '', $page = 1, $sort = null){
		$dbh = $this->dbhandle;
		//$dbh->setAttribute( PDO::ATTR_CASE, PDO::CASE_NATURAL );
		$itemsPerPage = 24;
		$page = is_int($page) ? $page : 0; 
		$skip = ($page > 0) ? ($page - 1) * $itemsPerPage : 0;
		$max = ($page === 0) ? 999999 : $skip + $itemsPerPage; // if page == 0 -> take all 
		$term = "%$term%";
		$firstLetter = "$firstLetter%";

		$sortStatement = 'ORDER BY img_id DESC'; // default
		//if (strtolower($sort) == 'date asc'){ $sortStatement = 'ORDER BY img_id ASC'; }
		if (strtolower($sort) == 'name-asc'){ $sortStatement = 'ORDER BY img_name ASC'; }
		if (strtolower($sort) == 'name-desc'){ $sortStatement = 'ORDER BY img_name DESC'; }
		
		try {
			$stmt = $dbh->prepare('SELECT COUNT(*) FROM image 
									WHERE 
										img_name LIKE :term AND 
										img_name LIKE :firstLetter
									');
			$stmt->bindParam(':term', $term);
			$stmt->bindParam(':firstLetter', $firstLetter);
	        $stmt->execute();
	        $rows = $stmt->fetchColumn(); 

			$stmt = $dbh->prepare("SELECT img_id as id, img_name as imageName
									FROM image 
									WHERE 
										img_name LIKE :term AND 
										img_name LIKE :firstLetter
										{$sortStatement}
										LIMIT :skip , :max
								  ");
			
			$stmt->bindParam(':term', $term);
			$stmt->bindParam(':firstLetter', $firstLetter);
			$stmt->bindParam(':skip', $skip, PDO::PARAM_INT);
			$stmt->bindParam(':max', $max, PDO::PARAM_INT);
	        $stmt->execute();

	        $mediaList->items = $stmt->fetchAll(PDO::FETCH_CLASS, 'MediaModel');
	        $mediaList->currentPage = $page;
	        $mediaList->totalPages = (int)ceil($rows / $itemsPerPage);
	      	
	        return true;
	    } 
	    catch (PDOException $e) {
	    	Logger::log($e);
		    return false;
		}
	}

	public function fetchImage(MediaModel &$mediaItem, $id){
		$dbh = $this->dbhandle;

		try{
			$stmt = $dbh->prepare('SELECT img_id as id, img_name as imageName FROM image WHERE img_id = :id');
			$stmt->bindParam(':id', $id);

			$stmt->setFetchMode(PDO::FETCH_CLASS, 'MediaModel');
			$stmt->execute();

			$mediaItem = $stmt->fetch();

			if($mediaItem == false){
				return false;
			}
			return true;
		}
		catch (PDOException $e){
			Logger::log($e);
			return false;
		}
	}

	public function deleteImage($id){
		$dbh = $this->dbhandle;

		try{
			$stmt = $dbh->prepare('SELECT COUNT(*) FROM exercise_image WHERE img_id = :id');
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();

			if ($stmt->fetchColumn() > 0) {
				$stmt = $dbh->prepare('SELECT ex_id FROM exercise_image WHERE img_id = :id');
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();
				$exercises = $stmt->fetchAll();

				return ['status' => false, 'exercises' => $exercises, 'imageName' => null ];
			}
			else {
				$stmt = $dbh->prepare('SELECT img_name FROM image WHERE img_id = :id');
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();
				$imageName = $stmt->fetchColumn();

				$stmt = $dbh->prepare('DELETE FROM image WHERE img_id = :id');
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();
				
				return ['status' => true, 'exercises' => null, 'imageName' => $imageName ];
			}
		}
		catch (PDOException $e){
			Logger::log($e);
			return false;
		}
	}

	public function addImage($imageName){
		$dbh = $this->dbhandle;

		try{
			$stmt = $dbh->prepare('INSERT into image (img_name) VALUES (:imageName)');
			$stmt->bindParam(':imageName', $imageName);
			$stmt->execute();

			return $dbh->lastInsertId();
		}
		catch (PDOException $e){
			//Logger::log($e);
			return false;
		}
	}

	public function renameImage($id, $imageName){
		$dbh = $this->dbhandle;

		try{
			$stmt = $dbh->prepare('UPDATE image SET img_name = :imageName WHERE img_id = :id');
			$stmt->bindParam(':imageName', $imageName);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$status = $stmt->execute();

			return ['status' => $status, 'msg'=> ($status) ? 'Image Renamed' : 'Image Rename Unsuccessfull!'];
			
		}
		catch (PDOException $e){
			Logger::log($e);
			return ['status' => false, 'msg'=> 'PDO error'];
		}

		
	}
}

?>