<?php
class ExerciseViewModelMapper extends BaseViewModelMapper{

    public function fetchAll(ExerciseListViewModel &$model){
       $modelFactory = new ModelFactory($this->dbhandle);

       $exerciseModelMapper = $modelFactory->buildMapper('ExerciseModelMapper');
       $exerciseModelMapper->fetchAll($model->exercises);

       parent::fetch($model);
    }
    public function fetchById(ExerciseListViewModel &$model, $id){
       $modelFactory = new ModelFactory($this->dbhandle);
       $exerciseModelMapper = $modelFactory->buildMapper('ExerciseModelMapper');
       $exercise;
       $exerciseModelMapper->fetchById($exercise, $id);
       $model->exercises[] = $exercise;

       parent::fetch($model);
    }
}
?>