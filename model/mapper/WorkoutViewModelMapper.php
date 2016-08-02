<?php

class WorkoutViewModelMapper extends BaseViewModelMapper{

    public function fetchAll(WorkoutListViewModel &$model){
    	$modelFactory = new ModelFactory($this->dbhandle);
    	
        $workoutModelMapper = $modelFactory->buildMapper('WorkoutModelMapper');

    	$workoutModelMapper->fetchAll($model->workouts);

    	parent::fetch($model);
    }
    public function fetchEmpty(WorkoutViewModel &$model){
    	$modelFactory = new ModelFactory($this->dbhandle);
    	
        $protocolModelMapper = $modelFactory->buildMapper('ProtocolModelMapper');
    	$protocolModelMapper->fetchAll($model->protocols);

    	parent::fetch($model);
    }
    /*public function fetchById(WorkoutViewModel &$model, $id){
        $modelFactory = new ModelFactory($this->dbhandle);
        
        $workoutModelMapper = $modelFactory->buildMapper('WorkoutModelMapper');
        $protocolModelMapper = $modelFactory->buildMapper('ProtocolModelMapper');
                
        $protocolModelMapper->fetchAll($model->protocols);
        $workoutModelMapper->fetchById($model->workout, $id);

        parent::fetch($model);
    }*/
}

?>