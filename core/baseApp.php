 <?php

class BaseApp{

	private $basepath = APP_BASE_PATH;
	private $dbhandle = null;
	private $dbhost = APP_DBHOST;
	private $dbname = APP_DBNAME;
	private $dbuser = APP_DBUSER;
	private $dbpass = APP_DBPASS;


	function __construct(){
		// Establish db connection
		$options = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
		
		try {
    		$this->dbhandle = new PDO('mysql:host=' . $this->dbhost . ';dbname=' . $this->dbname , $this->dbuser, $this->dbpass, $options);
    	}
		catch(PDOException $e)
    	{
    		Logger::log('an error occured: ' . $e->getMessage()); 
    		exit();
    	}

    	Logger::initLogger(@constant('DEVELOPMENT_ENVIRONMENT'));
    	
    	$this->createController();
	}

	function createController()
	{
		$requestURI = $_SERVER['REQUEST_URI'];
		$postVars = [];
		$getVars = [];
		$pathVars = [];
		$xssSecureHelper = new XssSecureHelper();

		// check basepath:
		if(substr($requestURI, 0, strlen($this->basepath)) === $this->basepath){
			$requestURI = substr($requestURI, strlen($this->basepath)); // remove basepath!
		} 
		else {
			Logger::log('Incorrect basepath!');
			exit();
		}
			
		$uri_segments = explode('?', $requestURI, 2); // Split Querystring from url.
		$pathSegments =  explode('/', trim($uri_segments[0], '/')); // Explode uri segments
		
		// Get controller and 
		$controllerName = ($pathSegments[0]) ? $pathSegments[0] . 'Controller' : $GLOBALS['defaultController'];	
		array_shift($pathSegments);
		
		$actionName = (count($pathSegments) > 0) ?  $pathSegments[0] . 'Action' : $GLOBALS['defaultAction'];	
		array_shift($pathSegments);

		$pathSegments = $xssSecureHelper->xssSecure($pathSegments);
	
		// Parse querystring data: .. Why not just use $_GET array?
		if(isset($uri_segments[1])){
			$tmp = [];
			parse_str($uri_segments[1], $tmp);
			$getVars = $xssSecureHelper->xssSecure($tmp);
		}

		if(isset($_POST) && count($_POST) > 0 ){
			// json decode if keyname is prefixed 'json-' .. Is this used anywhere??
			foreach ($_POST as $key => $value) {
	    		if (substr($key, 0, 5) == 'json-') {
	        		$_POST[substr($key,5)] = json_decode($value);
	    		}
			}
			$postVars = $xssSecureHelper->xssSecure($_POST);
		}		

		$routes = json_decode(file_get_contents('./routes.json'),true);

		$modelFactory = new ModelFactory($this->dbhandle);

		try{
			$args = [];
			if(!$this->checkRoutes($args, $controllerName, $actionName, $getVars, $postVars, $pathSegments, $_FILES, $routes)){
				throw new Exception("Error: route $controllerName->$actionName does not exist ");
			}
			if(!class_exists($controllerName))
				throw new Exception("Error: no such controller: $controllerName", 1);

			$controller = new $controllerName($modelFactory, null);

			if(!method_exists($controller, $actionName))
				throw new Exception("Error: Controller $controllerName has no action $actionName", 1);

			$controller->action($actionName, $args);
		}
		catch(Exception $e){
			Logger::log($e);
			$controller = new ErrorController($modelFactory, new Auth($this->dbhandle));
			$controller->action("PageDoesNotExistAction", $args);		
		}
	}

	function checkRoutes(&$args, $controllerName, $actionName, $getVars, $postVars, $urlVars, $fileVars, $routes){
		foreach($routes as $r) {
			if (strtolower($r['controller']) === strtolower($controllerName) && strtolower($r['action']) === strtolower($actionName)) {
		    	if (array_key_exists('urlVars', $r)){
		    		if (count($urlVars) != $r['urlVars']){ 
		    			continue;
		    		}
			    	foreach($urlVars as $k=>$u){
			    		$args["seg".$k] = $urlVars[$k];	
			    	}
			    }
		    	
		    	if (array_key_exists('getVars', $r)){
		    		foreach($r['getVars'] as $g){
			    		if(!array_key_exists($g, $getVars)){ 
		    				$args = [];
		    				continue(2);
		    			}
			    		$args[$g] = $getVars[$g];
			    	}	
		    	}
		    	
		    	if (array_key_exists('postVars', $r)){		    	
			    	foreach($r['postVars'] as $p){
			    		if(!array_key_exists($p, $postVars)){ 
		    				$args = [];
		    				continue(2);
		    			}
			    		$args[$p] = $postVars[$p];	
			    	}
			    }
			    if (array_key_exists('fileVars', $r)){		    	
			    	foreach($r['fileVars'] as $p){
			    		if(!array_key_exists($p, $fileVars)){ 
		    				$args = [];
		    				continue(2);
		    			}
			    		$args[$p] = $fileVars[$p];	
			    	}
			    }
		    	return true;
		  	}
		}
		return false;
	}
}

new BaseApp();

?>
