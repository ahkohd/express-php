<?php

/*
 * Router Class
 *
 * This is Express PHP router class that does all the URL routing jobs.
 * Router class, handles all the URL matching, compiling of route parameters into regex,
 * parsing route parameters.
 *
 * @copyright Copyright (c) 2012-2015 Danny van Kooten <hi@dannyvankooten.com>
 * @license MIT
 * @modifier Victor Aremu <victor.olorunbunmi@gmail.com>
 *
 * @modifier-note Modified Alto Router, such that it could fit into the Express PHP project perfectly.
 *
 */


class Router {

	/**
	 * @var array Array of all routes (incl. named routes).
	 */
	protected $routes = array();

	/**
	 * @var array Array of all named routes.
	 */
	protected $namedRoutes = array();

	/**
	 * @var string Can be used to ignore leading part of the Request URL (if main file lives in subdirectory of host)
	 */
	public $basePath = '';

	/**
	 * @var array Array of default match types (regex helpers)
	 */
	protected $matchTypes = array(
		'i'  => '[0-9]++',
		'a'  => '[0-9A-Za-z]++',
		'h'  => '[0-9A-Fa-f]++',
		'*'  => '.+?',
		'**' => '.++',
		''   => '[^/\.]++'
	);

	/**
	  * Create router in one call from config.
	  *
	  * @param array $routes
	  * @param string $basePath
	  * @param array $matchTypes
	  */
	public function __construct( $routes = array(), $basePath = '', $matchTypes = array() ) {
		$this->addRoutes($routes);
		$this->setBasePath($basePath);
		$this->addMatchTypes($matchTypes);
	}
	
	/**
	 * Retrieves all routes.
	 * Useful if you want to process or display routes.
	 * @return array All routes.
	 */
	public function getRoutes() {
		return $this->routes;
	}

	/**
	 * Add multiple routes at once from array in the following format:
	 *
	 *   $routes = array(
	 *      array($method, $route, $target, $name)
	 *   );
	 *
	 * @param array $routes
	 * @return void
	 * @author Koen Punt
	 * @throws Exception
	 */
	public function addRoutes($routes){
		if(!is_array($routes) && !$routes instanceof Traversable) {
			throw new \Exception('Routes should be an array or an instance of Traversable');
		}
		foreach($routes as $route) {
			call_user_func_array(array($this, 'map'), $route);
		}
	}

	/**
	 * Set the base path.
	 * Useful if you are running your application from a subdirectory.
	 */
	public function setBasePath($basePath) {
		$this->basePath = $basePath;
	}

	/**
	 * Add named match types. It uses array_merge so keys can be overwritten.
	 *
	 * @param array $matchTypes The key is the name and the value is the regex.
	 */
	public function addMatchTypes($matchTypes) {
		$this->matchTypes = array_merge($this->matchTypes, $matchTypes);
	}

	/**
	 * Map a route to a target
	 *
	 * @param string $method One of 5 HTTP Methods, or a pipe-separated list of multiple HTTP Methods (GET|POST|PATCH|PUT|DELETE)
	 * @param string $route The route regex, custom regex must start with an @. You can use multiple pre-set regex filters, like [i:id]
	 * @param mixed $target The target where this route should point to. Can be anything.
	 * @param string $name Optional name of this route. Supply if you want to reverse route this url in your application.
	 * @throws Exception
	 */
	public function map($method, $route, $target, $name = null) {

		$this->routes[] = array($method, $route, $target, $name);

		if($name) {
			if(isset($this->namedRoutes[$name])) {
				throw new \Exception("Can not redeclare route '{$name}'");
			} else {
				$this->namedRoutes[$name] = $route;
			}

		}

		return;
	}

	/**
	 * Reversed routing
	 *
	 * Generate the URL for a named route. Replace regexes with supplied parameters
	 *
	 * @param string $routeName The name of the route.
	 * @param array @params Associative array of parameters to replace placeholders with.
	 * @return string The URL of the route with named parameters in place.
	 * @throws Exception
	 */
	public function generate($routeName, array $params = array()) {

		// Check if named route exists
		if(!isset($this->namedRoutes[$routeName])) {
			throw new \Exception("Route '{$routeName}' does not exist.");
		}

		// Replace named parameters
		$route = $this->namedRoutes[$routeName];
		
		// prepend base path to route url again
		$url = $this->basePath . $route;

		if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {

			foreach($matches as $index => $match) {
				list($block, $pre, $type, $param, $optional) = $match;

				if ($pre) {
					$block = substr($block, 1);
				}

				if(isset($params[$param])) {
					// Part is found, replace for param value
					$url = str_replace($block, $params[$param], $url);
				} elseif ($optional && $index !== 0) {
					// Only strip preceeding slash if it's not at the base
					$url = str_replace($pre . $block, '', $url);
				} else {
					// Strip match block
					$url = str_replace($block, '', $url);
				}
			}

		}

		return $url;
	}

	/**
	 * Match a given Request Url against stored routes
	 * @param string $requestUrl
	 * @param string $requestMethod
	 * @return array|boolean Array with route information on success, false on failure (no match).
	 */
	public function match($requestUrl = null, $requestMethod = null) {

		$params = array();
		$match = false;

		// set Request Url if it isn't passed as parameter
		if($requestUrl === null) {
			$requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
		}

		// strip base path from request url
		$requestUrl = substr($requestUrl, strlen($this->basePath));

		// Strip query string (?a=b) from Request Url
		if (($strpos = strpos($requestUrl, '?')) !== false) {
			$requestUrl = substr($requestUrl, 0, $strpos);
		}

		// set Request Method if it isn't passed as a parameter
		if($requestMethod === null) {
			$requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
		}

		foreach($this->routes as $handler) {
			list($methods, $route, $target, $name) = $handler;

			$method_match = (stripos($methods, $requestMethod) !== false);

			// Method did not match, continue to next route.
			if (!$method_match) continue;

			if ($route === '*') {
				// * wildcard (matches all)
				$match = true;
			} elseif (isset($route[0]) && $route[0] === '@') {
				// @ regex delimiter
				$pattern = '`' . substr($route, 1) . '`u';
				$match = preg_match($pattern, $requestUrl, $params) === 1;
			} elseif (($position = strpos($route, '[')) === false) {
				// No params in url, do string comparison
				$match = strcmp($requestUrl, $route) === 0;
			} else {
				// Compare longest non-param string with url
				if (strncmp($requestUrl, $route, $position) !== 0) {
					continue;
				}
				$regex = $this->compileRoute($route);
				$match = preg_match($regex, $requestUrl, $params) === 1;
			}

			if ($match) {

				if ($params) {
					foreach($params as $key => $value) {
						if(is_numeric($key)) unset($params[$key]);
					}
				}

				return array(
					'target' => $target,
					'params' => $params,
					'name' => $name
				);
			}
		}
		return false;
	}

	/**
	 * Compile the regex for a given route (EXPENSIVE)
	 */
	private function compileRoute($route) {
		if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {

			$matchTypes = $this->matchTypes;
			foreach($matches as $match) {
				list($block, $pre, $type, $param, $optional) = $match;

				if (isset($matchTypes[$type])) {
					$type = $matchTypes[$type];
				}
				if ($pre === '.') {
					$pre = '\.';
				}

				$optional = $optional !== '' ? '?' : null;
				
				//Older versions of PCRE require the 'P' in (?P<named>)
				$pattern = '(?:'
						. ($pre !== '' ? $pre : null)
						. '('
						. ($param !== '' ? "?P<$param>" : null)
						. $type
						. ')'
						. $optional
						. ')'
						. $optional;

				$route = str_replace($block, $pattern, $route);
			}

		}
		return "`^$route$`u";
	}
}


/*
 * Request Class
 *
 * This is Express PHP Request class. The Request class is used to create the Request Object
 * which is used to handle/process user's request. Request class contains methods and properties
 * that are used to process a user's request.
 *
 * @copyright Copyright (c) Victor Aremu <victor.olorunbunmi@gmail.com>
 * @license MIT
 */

class Request {

	/**
     *@var array Array to store route parameters
     */
    public $params = array();

    /**
     * Setup Request Object
     * @author Victor Aremu
     */
	public function __construct() {

	}

	/**
     * Set @var header the HTTP HEADERS in the following format:
     * 		array('HEADER_NAME' => 'VALUE');
     */
    public function get_http_header() {

        /**
         * headers_list() @return HTTP HEADERS in the following format:
         * 		array('HEADER_NAME:VALUE');
         */
        $headers = headers_list();
        $headers_key_value = array();

        /**
         * Loop through $headers
         */
        for($i=0; $i < count($headers); $i++) {

            /**
             * Explode ':' in the current index's value
             */
            $chunks = explode(':', $headers[$i]);

            /**
             * Set $chunks[0] as key and $chunks[1] value of element in @var header
             */
            $headers_key_value[$chunks[0]] = $chunks[1];
        }

        return $headers_key_value;

    }

    /**
     * Magic Getter Useful for getting the value of undeclared class properties
     * on the fly. Useful my getting request entities on the fly
     * @return mixed
     * @param string $name The name of the undeclared property called
     * @author Victor Aremu
     */
     public function __get($name) {
     	switch($name) {
     		case 'query':
     			return $_GET;
     			break;
     		case 'body':
     			return $_POST;
     			break;
     		case 'header':
     			return $this->get_http_header();
     			break;
     		case 'cookies':
     			return $_COOKIE;
     			break;
     		case 'route':
     			return $_SERVER['REQUEST_URI'];
     			break;
     		case 'session':
     			return $_SESSION;
     			break;
     		case 'method':
     			return $_SERVER['REQUEST_METHOD'];
     			break;
     		default:
     		 return 'Class property '.$name.' not declared';
     	}
     }

    /**
     * Adds a new element to @var params in the following format
     * 		array('KEY', 'VALUE');
     */
    public function set_params($key, $value) {
       $this->params[$key] = $value;
    }	
}

/*
 * Response Class
 *
 * This is Express PHP Response class. The Response class is used to create the Response Object
 * which is used to handle/process user's response. Request class contains methods and properties
 * that are used to process a user's response.
 *
 * @copyright Copyright (c) Victor Aremu <victor.olorunbunmi@gmail.com>
 * @license MIT
 */

class Response {
    
    /**
     *@var string Path to Express view directory
     */
   	private $views;

   	/**
     *@var string Path to Express view directory
     */
   	private $basePath;

   	/**
     *@var boolean Enables or disables Express template caching
     */
    private $template_caching;

     /**
     *@var string Path to Express view cache directory
     */
    private $template_cache_dir;

    /**
     *@var string Name of view engine used by Express
     */
   	private $view_engine;

	/**
	 * Set Response view properties
	 * @param string $engine
	 * @param string $views
	 * @param string $caching
	 * @param string $cache_dir
	 */
    public function config_template($basePath, $engine, $views, $caching, $cache_dir) {
    	$this->basePath = $basePath;
    	$this->view_engine = $engine;
        $this->views = $views;
        $this->template_cache_dir = $cache_dir;
        $this->template_caching = $caching;
    }
    
	/**
	 * Sends a response(view) without using view engine
	 * @param string $content
	 * @param array $http_headers
	 */
    public function send($content, $http_headers) {
        foreach ($http_headers as $key => $value) {
            header($key.':'.$value); 
        }
        echo $content;
    }
    

    /**
	 * Renders a response(view) using view engine
	 * @param string $template
	 * @param array $data
	 */
    public function render($template, $data) {

        /**
         * Assign the shared global variables
         */

        foreach(Express::$sharedData as $key => $value ) {

            $data[$key] = $value;
        }



        /**
         * Switch between template engines
         */
        switch ($this->view_engine) {
        	case 'default':

        		/**
        		 * Case default, no view engine is used, we serve PHP view files
        		 */
        		include $this->views.'/'.$template.'.php';
        		break;
        	case 'smarty':

        		/**
        		 * Case smarty, set up the view engine and configure it
        		 */
	        	require 'express_modules/Smarty/Smarty.class.php';
	        	$smarty = new Smarty;

	        	# Configure smarty
	        	$smarty->template_dir = __DIR__.'/'.$this->views;
                	/**
                     * Enable caching if set
                     */
                    if($this->template_caching==true){
                    	echo $this->template_cache_dir.'_______-';
                        $smarty->cache_dir = __DIR__.'/'.$this->template_cache_dir;
                        $smarty->caching = true;
                      }



	        	/**
	        	 * Assign values
	        	 */
	        	foreach($data as $key => $value) {
	        		$smarty->assign($key,$value);
	        	}

	        	/**
	        	 * Load the view
	        	 */
	        	$smarty->display($template.'.tpl');
	        	break;
        }
    }

    /**
     * Sets HTTP Header
     * @param string $key
     * @param string $value
     */
    public function set($key, $value) {
    	# Add HTTP header
    	header($key.':'.$value);
    }

    /**
     * Sets COOKIE
     * @param string $name
     * @param string $value
     * @param string Time $expire
     */
    public function setCookie($name, $value, $expire) {
    	# Add a new cookie
    	setcookie($name, $value, $expire);
    }

    /**
     * Sets a SESSION data
     * @param string $key
     * @param string $value
     */
    public function setSession($key, $value) {
    	# Add a new element to $_SESSION 
    	$session_status = session_status();
      	if($session_status==2) {
            # Implies that session as been started, append element
            $_SESSION[$key] = $value;
        }
    }

    /**
     * Redirects user to another route
     * @param string $route
     */
    public function redirect($route) {
    	header('location:'.$this->basePath.$route);
    }

    /**
     * Sets HTTP status
     * @param string $route
     */
    public function status($code) {
    	http_response_code($code);
    }
}

/*
 * Express Class
 *
 * The Express class is the main entry point to Express PHP Framework. It is used to create the Express Object
 * which constructs the Express PHP frame work, providing route methods to ignite middle wares and router,
 * imports Express PHP modules, used in setting and getting Express PHP Configurations...
 *
 * @copyright Copyright (c) Victor Aremu <victor.olorunbunmi@gmail.com>
 * @license MIT
 */

class Express {
    
    /**
     * @var string Express base path
     */
    public $basePath;

    /**
     * @var string Express base path
     */
    public $staticPath;

    /**
     * @var array Array of app shared variables
     */

    static public $sharedData = array();

    /**
     * @var array holding error pages handler routes
     */
    public $errorPage = array();

    /**
     * @var object Router object
     */
    public $router;

    /**
     * @var array Array Contains Injected Express modules in the format:
     *		array('_MODULE_NAME'=>OBJECT);
     */
    private $modules;

    /**
     * @var array Array Contains Reverse names(identifiers) of routes as the keys and
     *      their second callback functions as value in a format:
     * 		array('route'=>'callback')
     * 		i.e array('/user/[i:uid]'=>$callback(new Request, new Response));
     */
    public $route_callback = array();

    /**
     * @var string Path to Express views directory
     */
    public $views;

    /**
     * @var string Express view engine name
     */
    public $view_engine;

    /**
     * @var array Array Register that holds callbacks for route specific middle wares in format:
     * 		array('route'=>array('middleware1_callback', 'middleware2_callback'))
     */
    public $route_middlewares =  array();
    
    /**
     * @var string Express Application environment state variable
     */
    public $env='development';

    /**
     * @var boolean Enables or disables view template caching
     */
    public $template_caching;

    /**
     * @var string Express view  template cache directory
     */
    public $template_cache_dir;
    
    /**
     * Construct Express Application
     * @author Victor Aremu
     */
    public function __construct() {

    	# Set the Error pages
    	//301 error Moved Permanently
    	$this->errorPage['301'] = null;

    	// 401 error Unauthorized
    	$this->errorPage['401'] = null;
    	// 404 error Not found
    	$this->errorPage['404'] = null;
    	// 500 error Internal Server error
    	$this->errorPage['500'] = null;


        # Set the Express base path as '/' by default...
        $this->basePath = '/';

        # Set the Express static path as '/' by default...
        $this->staticPath = '/';

        # Instance a new Router class...
        $this->router = new Router();

        # Set Express default template engine...
        $this->view_engine = 'default';

       # Disable view template caching by default...
        $this->template_caching = FALSE;
    }

    /**
     * Import Module into Express
     * @param string $module_name
     */

    public function import($module_name) {
    	$path = "express_modules/".$module_name."/index.php";
    	if(file_exists($path)) {
    		include $path;
    	} else {
    		throw new Exception('Unable to Load Module: '.$module_name);
    	}
    }
    
    /**
     * Magic function __set Invokes when user set a value to a undefined class property
     * Useful for Registering Express modules
     * @param string $name
     * @param mixed $value
     */
	public function __set($name, $value) {

		/**
		 * Check if the property name meets Express Module Naming convention format:
		 *     Underscore followed by module name in pascal case
		 *		_ModuleName
		 */
		if((strpos($name, '_')!==FALSE) && (strpos($name, '_')===0)) {

		    /**
		     * It Match Express Modules naming convention
		     * Register the module
		     */
		    $this->modules[$name] = $value;
		} else {
		      throw new Exception ('Cannot set '.$name.'. Not a defined class property', 1);
		}
	}

	/**
	 * Magic function __get() Invokes when user calls Object properties that does not exists
	 * Useful for getting Express Injected Modules
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {

     	/**
     	 * Check if the user is trying to get a module
     	 */
    	if((strpos($name, '_')!==FALSE) && (strpos($name, '_')===0)) {

        	/**
        	 * TRUE => $name Match Express Module naming convention
        	 * Return the appropriate module object
        	 */

        	# Check If module exits...
        	if(array_key_exists($name, $this->modules)){

          		# Module exists return module object...
          		return $this->modules[$name];  
        	} else {

	           # Module does exists return NULL...
    	      return NULL;
        	}
      	} else {
        	throw new Exception($name.' not a defined class property', 1);
      	}
	}

    /**
     * Magic function __call Invokes when user calls Object method that does not exists
     * Useful for overloading Object methods
     * @param string $method_name
     * @param array $parameter
     */
    public function __call($method_name, $parameter) {

    	/**
    	 * Overloaded methods includes:
    	 *		$Object->use($param1);
    	 *		$Object->use($param1, $param2);
    	 *		$Object->get($param1);
    	 *		$Object->get($param1, $param2);
    	 */
    	if($method_name=="use") {

    		# Overloaded Use method goes here...
    		$count = count($parameter);

    		switch ($count) {
    			case '1':

    				# Global middle ware logic...

                    /**
                     * Invoke the middle ware
                     */
                    $parameter[0];
    				break;
    			case '2':
    				# Route specific middle ware...
                    
                    /**
                     * $parameter[0] => middle ware name
                     * $parameter[1] => middle ware callback function
                     */
                                
                     # Check if the middle ware is already registered...
                     if(array_key_exists($parameter[0], $this->route_middlewares)) {

                        /**
                         * Implies that this middle ware as been registered
                         * The user is trying to append a new middle ware to that route
                         */
                         array_push($this->route_middlewares[$parameter[0]], $parameter[1]);
                     } else {
                    
                        /**
                         * The user hasn't registered that middle ware before
                         */
                         $this->route_middlewares[$parameter[0]] = array();
                         array_push($this->route_middlewares[$parameter[0]], $parameter[1]);
                     }            
    				 break;
    		}
                
            if($count==0 || $count>2) {
            	throw new Exception('Bad Argument');
            }
        } else if($method_name=="get"){

        	#Overloaded get method goes here..
            $count_get = count($parameter);
            switch ($count_get) {
                case '1':

                    # Overloaded function to get Express configuration details...
                    switch(strtolower($parameter[0])) {
                        case 'basepath':
                            return $this->basePath;
                            break;
                        case 'static':
                        	return $this->staticPath;
                        	break;
                        case 'views':
                            return $this->views;
                            break;
                        case 'view engine':
                            return $this->view_engine;
                            break;
                        case 'env':
                            return $this->env;
                            break;
                        case 'view cache':
                            return $this->template_caching;
                            break;
                        case 'view cache path':
                            return $this->template_cache_dir;
                            break;
                        case 'error 301':
                         	return $this->errorPage['301'];
                            break;
                        case 'error 401':
                         	return $this->errorPage['401'];
                            break;
                        case 'error 404':
                         	return $this->errorPage['404'];
                            break;
                        case 'error 500':
                         	return $this->errorPage['500'];
                            break;
                     }
    
                    break;
                case '2':

                    # Overloaded function to route HTTP post request...

                	/**
                	 * @param string $route
                	 * @param function $callback
                	 */
                    $route = $parameter[0];
                    $callback = $parameter[1];

                    /**
                     * The GET HTTP request route function
                     * Set this route reverse name in format:
                     * 		$route-$_SERVER['REQUEST_METHOD']
                     */
                    $route_reverse_name = $route.'-GET';

                    /**
                     * Append an  array into the @var $route_callback[] using the reverse name as a key
                     * and set the value to the callback function for this route
                     */
                    $this->route_callback[$route_reverse_name] = $callback;

                    # Map the route; inside this 1st call back function, invoke the 2nd callback...
                    $this->router->map('GET', $route, function($call, $params){

                        # Instance a Request object...
                        $req = new Request();

                        # Instance a Response object...
                        $res = new Response();

                        # Configure view engine...
                        $res->config_template($this->basePath, $this->view_engine, $this->views, $this->template_caching, $this->template_cache_dir);

                        # Loop through the route parameters and map the values...
                        foreach ($params as $key => $value) {
                            $req->set_params($key, $value);
                        }

                        # Get current route
                        $get_route = str_replace($this->basePath, '', $_SERVER['REQUEST_URI']);
                        
                        /**
                         * Execute route specific middle wares
                         */

                        # loop through the @var $route_middlewares...
                        foreach ($this->route_middlewares as $key => $value) {
                            // flag here
                            // if($key==$get_route) {

                            if(strpos($get_route, $key) !== false) {

                                # The middle ware's name match this route, execute the middle ware callback...
                                $count_call = count($value);
                                for($a=0; $a<$count_call; $a++) {
                                    
                                    # Invoke the middle ware, pass request and response object...
                                    $value[$a]($req, $res);
                                }
                            }
                        }

                        $call($req, $res);
                    }, $route_reverse_name);   
                    break;
            }
            
            if($count_get==0 || $count_get>2) {
                    throw new Exception('Bad Argument');
                }
                
        } else if($method_name=="setGlobal") {

            $count = count($parameter);
            if($count==0 || $count>2) {
                throw new Exception('Bad Argument');
            } else
            {
                Express::$sharedData[$parameter[0]] = $parameter[1];
            }

        } else if($method_name=="getGlobal") {

                return Express::$sharedData[$parameter[0]];
        }else {
    		throw new Exception('Function '.$method_name.' does not exists.');
    	}
    }
    
    /**
     * The POST HTTP request route method
     * @param string $route
     * @param function $callback
     */
    public function post($route, $callback) {

        /**
         * The POST HTTP request wrapper function
         * Set this route reverse name to $route
         */
        $route_reverse_name = $route.'-POST';  

        /**
         * Append an  array into the route_callback[] using the reverse name as a key
         * and set the value to the callback function for this route
         */

        $this->route_callback[$route_reverse_name] = $callback;

        # Map the route; inside this 1st call back function, invoke the 2nd callback...
        $this->router->map('POST', $route, function($call, $params){
        
        # Instance a Request object...
        $req = new Request();
         
        #Instance a Response object...
        $res = new Response();
        
        # Configure view engine...
        $res->config_template($this->basePath, $this->view_engine, $this->views, $this->template_caching, $this->template_cache_dir);
        
        # Loop through the URL parameters and map the values...
        foreach ($params as $key => $value) {
            $req->set_params($key, $value);
        }
        
        # Get the current route...      
        $get_route = str_replace($this->basePath, '', $_SERVER['REQUEST_URI']);

        /**
         * Execute route specific middle wares
         */

        # Loop through the route_middlewares...
        foreach ($this->route_middlewares as $key => $value) {
            if($key==$get_route) {

                # The middle ware's name match this route, execute the middle ware...
                $count_call = count($value);
                for($a=0; $a<$count_call; $a++) {

                    # Invoke the middle ware, pass request and response object...
                    $value[$a]($req, $res);
                }
            }
        }
                
        $call($req, $res); }, $route_reverse_name);
    }
 
 
    public function put($route, $callback) {
        // The PUT HTTP request wrapper function
            // Set this route reverse name to $route
            $route_reverse_name = $route.'-PUT';      
            // Append an  array into the route_callback[] using the reverse name as a key
            // and set the value to the callback function for this route
            $this->route_callback[$route_reverse_name] = $callback;
            // Map the route; inside this 1st call back function, invoke the 2nd callback
            $this->router->map('PUT', $route, function($call, $params){
                // Instance a Request object
                $req = new Request();
                // Instance a Response object
                $res = new Response();
                // Configure templating
                $res->config_template($this->basePath, $this->view_engine, $this->views, $this->template_caching, $this->template_cache_dir);
                // Loop through the url parameters and map the values
                foreach ($params as $key => $value) {
                    $req->set_params($key, $value);
                }
                
                                $get_route = str_replace($this->basePath, '', $_SERVER['REQUEST_URI']);
                ///////////////// EXECUTE ROUTE SPECIFIC MIDDLEWARES
                // loop through the route_middlewares
                foreach ($this->route_middlewares as $key => $value) {
                    if($key==$get_route) {
                        // The middleware's name match this route, execute the middleware
                        $count_call = count($value);
                        for($a=0; $a<$count_call; $a++) {
                            // Invoke the middleware, pass request and response object
                            $value[$a]($req, $res);
                        }
                    }
                }
                
                $call($req, $res);
            }, $route_reverse_name);
    }
    
    public function patch($route, $callback) {
        // The PATCH HTTP request wrapper function
            // Set this route reverse name to $route
            $route_reverse_name = $route.'-PATCH';      
            // Append an  array into the route_callback[] using the reverse name as a key
            // and set the value to the callback function for this route
            $this->route_callback[$route_reverse_name] = $callback;
            // Map the route; inside this 1st call back function, invoke the 2nd callback
            $this->router->map('PATCH', $route, function($call, $params){
                // Instance a Request object
                $req = new Request();
                // Instance a Response object
                $res = new Response();
                // Configure templating
                $res->config_template($this->basePath, $this->view_engine, $this->views, $this->template_caching, $this->template_cache_dir);
                // Loop through the url parameters and map the values
                foreach ($params as $key => $value) {
                    $req->set_params($key, $value);
                }
                
                                $get_route = str_replace($this->basePath, '', $_SERVER['REQUEST_URI']);
                ///////////////// EXECUTE ROUTE SPECIFIC MIDDLEWARES
                // loop through the route_middlewares
                foreach ($this->route_middlewares as $key => $value) {
                    if($key==$get_route) {
                        // The middleware's name match this route, execute the middleware
                        $count_call = count($value);
                        for($a=0; $a<$count_call; $a++) {
                            // Invoke the middleware, pass request and response object
                            $value[$a]($req, $res);
                        }
                    }
                }
                
                $call($req, $res);
            }, $route_reverse_name);
    }
    
    public function delete($route, $callback) {
        // The DELETE HTTP request wrapper function
            // Set this route reverse name to $route
            $route_reverse_name = $route.'-DELETE';      
            // Append an  array into the route_callback[] using the reverse name as a key
            // and set the value to the callback function for this route
            $this->route_callback[$route_reverse_name] = $callback;
            // Map the route; inside this 1st call back function, invoke the 2nd callback
            $this->router->map('DELETE', $route, function($call, $params){
                // Instance a Request object
                $req = new Request();
                // Instance a Response object
                $res = new Response();
                // Configure templating
                $res->config_template($this->basePath, $this->view_engine, $this->views, $this->template_caching, $this->template_cache_dir);
                // Loop through the url parameters and map the values
                foreach ($params as $key => $value) {
                    $req->set_params($key, $value);
                }
                
                
                                $get_route = str_replace($this->basePath, '', $_SERVER['REQUEST_URI']);
                ///////////////// EXECUTE ROUTE SPECIFIC MIDDLEWARES
                // loop through the route_middlewares
                foreach ($this->route_middlewares as $key => $value) {
                    if($key==$get_route) {
                        // The middleware's name match this route, execute the middleware
                        $count_call = count($value);
                        for($a=0; $a<$count_call; $a++) {
                            // Invoke the middleware, pass request and response object
                            $value[$a]($req, $res);
                        }
                    }
                }
                
                
                $call($req, $res);
            }, $route_reverse_name);
    }

    
    public function set($name, $value) {
        
        // This function is used to set, app's configuration
        switch(strtolower($name)) {
             case 'basepath':
             $this->basePath = $value;
             $this->router->setBasePath($this->basePath);
                 break;
             case 'static':
             	$this->staticPath = $value;
             	break; 
              case 'views':
              $this->views = $value;
              	break;
              case 'view engine':
              $this->view_engine = $value;
              	break;
            case 'env':
              $this->env = $value;
              	break;
             case 'view cache':
                 $this->template_caching = $value;
                 break;
             case 'view cache path':
                 $this->template_cache_dir = $value;
                 break;
            case 'error 301':
                 $this->errorPage['301'] = $value;
                 break;
            case 'error 401':
                 $this->errorPage['401'] = $value;
                 break;
            case 'error 404':
                 $this->errorPage['404'] = $value;
                 break;
            case 'error 500':
                 $this->errorPage['500'] = $value;
                 break;

        }
    }
 
    public function __destruct() {

        // Invoke __destruct(), let match the current request 
         $match = $this->router->match();

         // If a match is found
         if($match && is_callable($match['target'])) { 

             // What I want to do here is just to let route callback be the first item in the params array
             // and request parameters should be stored as an array in two the 2nd element of the params array
             // Create a new array
             $new_params = array();

             // Make call the first item in the array
             // But before that let us get the one which was matched
             // to do this we have to loop through the route_callback, use $match['name'] to get
             // the reverse name of the route which was matched.
             // if $match['name'] is equals to our current iterator's key, Yo!
             // We have found the right callback item, set it to be the first item in the $new_params array.
             foreach ($this->route_callback as $key => $value) {
                 if($match['name']==$key) {
                     $new_params['call'] = $this->route_callback[$key];
                 }
             }
             // Get the length of $match['params'] which contains request parameters variables
             $len = count($match['params']);
             // Create an array to store request parameters variables
             $variables = array();
             // loop through $match['params'] array and the append them into $variables
             foreach ($match['params'] as $key => $value) {
              $variables[$key] = $value;   
             }
             // Set 'params' as the second key in the new array
             $new_params['params'] = $variables;
             // Overwrite $match['params'], with the new one we've just prepared
             $match['params'] = $new_params;
             call_user_func_array($match['target'], $match['params']);
         }  else {

             // NO route was matched
         	 // Set HTTP response status 404 - Page Not found
             http_response_code(404);

         	 // Check there is a route that is dedicated to handle 404 error
         	 if($this->errorPage['404']==null) {
         	 	echo 'Cannot '.$_SERVER['REQUEST_METHOD'].' '.str_replace($this->basePath, '', $_SERVER['REQUEST_URI']);
         	 } else {
         	 	
         	 	// Found! Then redirect to the route
         	 	header("location:".$this->basePath.$this->errorPage['404']);
         	 }
         		
             
         }    
    }
   
}
