<?php
class ProtocolViewModelMapper extends BaseViewModelMapper{

    /*public function fetchAll(ExerciseListViewModel &$model){
       $modelFactory = new ModelFactory($this->dbhandle);

       $exerciseModelMapper = $modelFactory->buildMapper('ExerciseModelMapper');
       $exerciseModelMapper->fetchAll($model->exercises);

       parent::fetch($model);
    }*/
    public function fetchById(ProtocolViewModel &$model, $id){
       $modelFactory = new ModelFactory($this->dbhandle);
       $protocolModelMapper = $modelFactory->buildMapper('ProtocolModelMapper');
       $protocolModelMapper->fetchById($model->protocol, $id);

       parent::fetch($model);
    }
}
?>