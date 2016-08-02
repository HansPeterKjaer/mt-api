<?php

class ErrorController extends BaseController {
	public function PageDoesNotExistAction(){
		$viewModel = $this->modelFactory->buildObject('BaseViewModel');
		$mapper = $this->modelFactory->buildMapper('BaseViewModelMapper');
		$mapper->fetch($viewModel);

		http_response_code(404);
		$this->view->output('PageDoesNotExistView', $viewModel, 'Shared/emptyTemplate');	
	}
}

?>