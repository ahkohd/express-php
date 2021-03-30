# Get Started With Express PHP Framework
## SETTING UP YOUR FIRST EXPRESS PHP APP
For setting up your first Express PHP App, the following procedures are to be taken:

1. Clone the this repository
2. Create or edit index.php file
	```php
	<?php
		
	// Require Express file
	require 'Express.php';

	// Instance a new Express App Object
	$app = new Express();
		
	// Express Configurations..

	// Set Express router's base path...

	// Set your App's base path. i.e where you extracted the app
	 $app->set('basePath', '/express');

	// Set Express view engine...

	// Set your App's view engine. By default Expess PHP supports Smarty view engine
	// You can set it to default, if you don't want to render your views with any view engine
	// if set to default your view template would be php files
	 $app->set('view engine', 'smarty');

	// Set Express views path...

	// Set the directory where your app's view template will reside
	$app->set('views', 'views/');


	// ROUTES...

	// Match a GET HTTP request to '/'
	$app->get('/', function($request, $response){

		// SEND BACK A MESSAGE TO THE BROWSER
		$response->send('Express PHP', array(
				'Content-type' => 'text/html'
		));
	});

	?>
	```

3. Create .htaccess file add the following
	```
	RewriteEngine On
	RewriteCond %{REQUEST_FILENAME} !-
	RewriteRule ^(.*)$ index.php/$1 [L]
	```
Spark! Lunch the address http://localhost/express on your browser, the following would be displayed:
		_Express PHP_

## Express PHP file structure
Express PHP does not force you to use the MVC style. Since it is like a port of Express JS, it file structure almost looks like that of Express JS

```
|- express_modules/							// Where Express PHP modules resides
|- cache/									// Where Express caches are stored
	|-views/									// Where Express views cache is stored...
|- views/ 									// Where Express views template are stored
|- public/									// Where Express static files i.e JS, CSS, images e.t.c are stored
|- Express.php								// The Express PHP file
|- index.php								// Entry point to Express PHP app
|- .htaccess								// Express .htaccess configuration
```
### express_modules/
This is where Express PHP modules are stored. Express PHP built modules also reside here.
Example include:
1. Smarty
2. ExpressValidator

### cache/
This is where Express PHP caches reside.

#### View Cache
This is where Express view template caches are stored.
**NOTE:** If you set your view engine to default, you don't need to set up view caching.

```
|- cache/views/
```

**NOTE:** that you can change it to another folder, All you have to do is create the folder and
use Express PHP setter method to set configuration.

```php
$app->set('view cache path', 'your/new/cache/folder/');
```

You can even disable template caching using the following snippet. By the way, by default template caching is disabled.

```
$app->set('view cache', FALSE);
```

### views/
This is where Express app view template resides.
**NOTE:** If you wish to change the folder, create your folder and use the following
snippet to configure Express to that folder you created.

```php
$app->set('views', 'your/new/path/to/view/templates/');
```

Express PHP views are rendered using template engine you set.
Express PHP supports _Smarty_ view engine. To use enable it use the snippet below:

```php
$app->set('view engine', 'smarty');
```

By default, view engine is set to _'default'_. Express PHP will render pure PHP files as templates:

```php
$app->set('view engine', 'default');
``` 

### public/
This is where Express PHP static files reside. File that resides here are directly accessible by the  browser.
**NOTE:** By default, the public folder is used. If you desire to change it use the following snippet:

```php
$app->set('static', 'path/to/new/public/folder/');
```

### Express.php
This is the Express PHP Framework code base. One of the aim of this frame work is been light weight. In fact this is all you need to create a Express PHP app.

### index.php
This is your Express App entry point. It is where your business logic resides

### .htaccess
Here is where we use mod_rewrite to rewrite Express PHP URL to a pretty one

## Express PHP App Structure
The following code depicts Express PHP app Structure:
```php
<?php

// REQUIRE EXPRESS PHP FRAMEWORK

// CREATE THE APP

// Configure Express PHP

// IMPORT EXPRESS MODULES

// SET YOUR EXPRESS PHP APP MIDDLEWARES

// ROUTING

?>
```
### REQUIRE/INCLUDE EXPRES PHP FRAMEWORK
In other to use Express PHP you have to include it your 'index.php' file
``` php
require 'Express.php';
```

### CREATING Express PHP APP
After including Express PHP framework. You have to create an Express PHP app. To do so you instance a new Express object which your app object
``` php
$app = new Express();
```

### CONFIGURING Express PHP App
After creating Express PHP app, you must configure the app in other for it to work properly. In Express Framework;
the `$app->set();` is used to set Express PHP application Configuration.

`$app->set();` accepts two arguments, which are:
- The name of the configuration and
- The value you want to set it to.

#### Express APP CONFIGURATIONS INCLUDE: 
- `basepath` Express APP base path.
	**Example:**
	```php
	$app->set('basepath', 'path/to/where/you/install/express/');
	```
- `static` Express App static path, where static files such as CSS, JS... are stored.
	```php
	$app->set('static', 'path/to/your/app\'s/static/folder');
	```

- `views` 	 Where your Express app view templates resides.
	**Example:**
	```php
	$app->set('views', 'path/to/where/your/view/template/resides/');
	```
- `view engine`  The name of the view engine want to use. By default you use either `default` which as its template extension as `.php` or `smarty` which it's template extension is `.tpl`
	**Example 1:** Using no view engine to render view.	
	NOTE: Linux users; In other to use smarty, make sure you chmod 775 where your view templates reside.
	```php
	$app->set('view engine', 'default');
	```

	**Example 2:** Using Smarty view engine to render view.
	```php
	$app->set('view engine', 'smarty');
	```
- `env` To set Express environment mode.
	The following are possible values
	1. development
	2. production
	3. test
	4. debug
	
	**Example:** Set Express mode to production
	```php
	$app->set('env', 'production');
	```
- `view cache`   To enable of disable view caching
	This configuration is used to enable or disable view template caching. It accepts boolean values, true or false
	**Example 1:** Enable view caching.
	```php	
	$app->set('view cache', true);
	```

	**Example 2:** Disable view caching.
	```php		
	$app->set('view cache', false);
	```

- `view cache path`  Set where Express will cache the view templates to.
	```php
	$app->set('view cache path', 'path/to/where/you/want/express/to/cache/its/view/template/');
	```

- `error 301` Set a route to handle when a 301 - Moved Permanently error occurred.
	```php
	$app->set('error 301', '/301-error-handler-route');
	```

- `error 401` Set a route to handle when a 401 - Unauthorized  error occurred.
	```php
	$app->set('error 401', '/401-error-handler-route');
	```
- `error 404` Set a route to handle when a 404 - Not Found error occurred. If not set, the default error handler will be use
	to handle the error. It will give an output in the following format:
		`Cannot {$_SERVER['REQUEST_METHOD']} {$basePath} {$route} `
	For example: When I browse a route that is not defined i.e `/undefined-route`, the custom error handler will return:
		`Cannot GET /undefined-route`
	To Your own customized 404 error handler:
	```php
	$app->set('error 404', '/404');
	$app->get('/404', function($req, $res){
	  $res->status(404);
	  $res->send('<h1>My customized 404 handler - Page Not Found</h1>', array(
	    'Content-Type' => 'text/html'
	    ));
	  
	});
	```
- `error 500` Set a route to handle when a 500 - Internal Error  error occurred.
	```php
	$app->set('error 500', '/500-error-handler-route');
	``

### IMPORT EXPRESS MODULES
Express supports modular coding. It supports a special type of dependency injection, where you inject your predefined
Modules into Express PHP in a seamless manner.

#### THE Express PHP MODULE
All Express PHP modules resides in the `express_modules/` folder. Let's see the a MODULE structure in Express PHP
```
|- express_modules/			// Where all ExpressPHP modules resides
	|- MyModuleName/			// Where your module files resides (ModuleName in PascalCase)
		|- index.php				// Entry point to your module, this is where Express PHP will import
		|- MyModuleName.php			// This is where your module business logic resides
									// (logic file Named after the ModuleName in PascalCase)
```
> ExpressPHP employs the use of `PascalCase` in naming it modules.

#### CREATING A EXPRESS PHP MODULE
Let's create a simple module that reverse  given string just for example. Let name it `ReverseString`.
The following steps are to be taken:
1. Inside the `express_modules/` folder create a folder named `ReverseString/`
2. Create two files inside the folder you created:
	- index.php
	- ReverseString.php
	_Your folder structure should now look like this._
	```
	|- express_modules/
		|- ReverseString/
			|- index.php
			|- ReverseString.php
	```
3. Write your Module's logic in the `ReverseString.php`
   ```php
   class ReverseString {
		public function reverse($string) {
			return str_rev($string);
		}
	}
	```
4. Now write your module entry point, in other for you to `export/inject it into Express PHP.
   Here is a template of creating your module entry point.
   ```php
   <?php

   # Require ModuleName Class Definition...
   require 'ModuleName.php';

 
   $export = function(){

   	// Instance the module...
   	$module = new ModuleName();

   	// Inject Module into Express...
   	$GLOBALS['app']->_ModuleName = $module; 

   	// NOTE:  ModuleName here must be prefixed with Underscore(_)
   	//	This tells Express PHP that this property is a module, and you
   	// are injecting it into Express() Object.
   };

   # Export module..
   $export();
   ```
   Edit your `index.php` add the following codes:

   ```php
   <?php

   // Require ReverseString Class Definition...
   require 'ReverseString.php';
   $export = function(){
		
   // Instance the module...
   $module = new ReverseString();

   // Inject Module into Express...
   $GLOBALS['app']->_ReverseString = $module;
   };

   // Export module..
   $export();
   ```
#### IMPORTING EXPRESS PHP MODULE		
To import a module in Express, we do this using the import() method.
```php
$app->import('ModuleName');
```
Now Let's Import the module we created earlier:
```php
$app->import('ReverseString');
// You have successfully imported the module...
```

#### Using Express PHP Module
To use the module you have imported into Express PHP. You access the Module's Object in general this format:
`$app->_ModuleName`
**NOTE:** We Standardize appending underscore before your Module Object so as to differentiate Your Module and `Express()` Object property. So I Express PHP whenever you see `$app->_Anything`, it is a module which as been injected 
into `Express ()` Object.
Let use the ReverseString module we created earlier, to reverse a string:
 ```php
 $app->_ReverseString->reverse('Hello World');
 //OUTPUT: dlroW olleH
 ```
 **Example:**

``` php
<?php
include 'Express.php';
$app = new Express();
$app->set('basePath', '/express');
$app->import('ReverseString');
echo $app->_ReverseString->reverse('Hello world');
// OUTPUT: dlroW olleH
?>
```
When you launch this in your browser, You will see...
_dlroW olleH_

Some modules that comes bundled with Express PHP:
1. `ExpressValidator`
	**Example:** `ExpressValidator` Module in action. Let's see if a specified string match the email form criteria.
	```php
	$app->import('ExpressValidator');
	var_dump($app->_ExpressValidator->validate('email', 'victor.olorunbunmi@gmail.com'));
	// OUTPUT: boolean TRUE
	```

### SET YOUR EXPRESS PHP APP MIDDLEWARES

#### What is a middleware?
As the name suggest, Middleware acts as a middle man between request and response. It is a type of filtering mechanism.
For example a middleware can be set to verify whether user of the application is authenticated or not.
If the user is authenticated, he will be redirected to the home page otherwise, he will be redirected to the login page.
There are two types of middlewares in Express PHP
* General Middleware, (Invoking middleware)
* Route Specific Middleware

#### The `USE()` METHOD
In Express PHP. We use `$app->use()` a method of ExpressPHP object to declare a middleware.
The `$app->use()` method is an overloaded method:
* `$app->use($arg1);`		// For declaring a general middleware
* `$app->use($arg1, $arg2);` // For declaring a route specific middleware

#### General Middleware (Invoking Middleware)
The general middleware is called the invoking middleware because it accepts a function and invokes it. Simple!
And it's is called general because it will be invoked even before URL routing begins, this implies that all routes will
be middlewared by the general middleware.
**Syntax:** `$app->use(function());`
**Note:** You pass in a function with the invoke operator as an argument to the method as depicted above.

##### Declaring A General Middleware
Lets declare a simple General middleware that echo 'hello I am a middleware' on every routes.
```php
function middleware1() {
	echo 'Hello I am a middleware';
}

$app->use(middleware1());
```
A practical use of the General middleware is using it to start a user session.
**Example:** This is a General middleware that initiates a user using ssession_start() every route.
```
$app->use(session_start());
```

#### How Route Specific Middlewares works in Express PHP
Once User Request for a route. Express PHP gets its Middlewares register and checks if there are any middleware 
callback function registered for this route requested. If found
it will invoke the callback and after that Express PHP now invokes your route's callback.

##### Declaring Route Specific Middleware
Middleware is an amazingly useful pattern that allows developers to reuse code within their applications and even
share it with others in the form of Express PHP modules. The essential definition of middleware is a function with two
arguments: request `(or $req)`, response `($res)`. Here’s an example of how to define your own middleware:
The second overloaded `$app->use()` method which accepts two parameters is used to declare a route specific middleware.

> $app->use(@route, @callback);
> @route: Is a string representing the route you want to add the middleware
> @callback: Is the function is invoked a user request matches the route, before the route's callback function is invoked. 
> When you define @callback, you give it two parameters:
> 1. $req // Which is the request object
> 2. $res // Which is the response Object

**Example:**
```php
$app->use('/', function($req, $res){
	// Your route specific middleware logic goes here...
});
```
_For Example_ Let's create a simple Logged In user authentication using the General, Route specific middleware and the Request Object:
```php

// Declare a General middleware that initiates a user session 
$app->use(session_start());

// Create a route specific middleware that executes its when the user visits the dashboard route
$app->use('/dashboard', function($req, $res){

  if(isset($req->session['loggedIn']) && $req->session['loggedIn']==FALSE) {

  // User has not logged in, redirect to the login page
  $res->redirect('/login');
 } else {
  // User is logged in! Send A message
  $res->send('Hello '.$req->session['username'], array('Content-type'=>'text/html'));
}
});
```

**NOTE:** A single route can have more than one middleware and all will be invoked.
**Example:** An example showing a route that has three middleware:

```php
$app->use('/', function($req, $res){
	echo "middleware 1";
});

$app->use('/', function($req, $res){
	echo "middleware 2";
});

$app->use('/', function($req, $res){
	echo "middleware 3";
});
```

### ROUTING
Routing is meant to route your request to an appropriate.
Express PHP provides a way to organize routes into smaller subsections (Routers—instances of Router class/object). In the only way to define routes is to use the `$app->VERB()` pattern, which we’ll cover next.

#### app.VERB()
Each route is defined via a method call on an application object with a URL pattern as the first parameter (regular
expressions2 are also supported); that is, `$app->METHOD(path, [callback...], callback)`.
For example, to define a **GET** `/api/v1/stories` endpoint:
```php
$app->get('/api/v1/stories/', function($request, $response){
  // ...
});
```
Or, to define an endpoint for the `POST HTTP` method and the same route:

```php
$app.post('/api/v1/stories', function($request,$ response){
  // ...
});
```
`DELETE`, `PUT`, and `PATCH` are supported as well.
The callbacks that we pass to `get()` or `post()` methods are called **request handlers**, because they take requests `$req`, process them, and write to the response `$res` objects. For example:
```php
$app->get('/about', function($request, $response){
	$res->send('About Us: ...', array('Content-type'=>'text/html'));
});
```
The following are the most commonly used Representational State Transfer (REST) server architecture HTTP
methods and their counterpart methods in Express PHP along with the brief meaning:
- GET: `$app->get()` Retrieves an entity or a list of entities.
- POST: `$app->post()` Submits a new entity.
- PUT: `$app->put()` Updates an entity by complete replacement.
- PATCH: `$app->patch()` Updates an entity partially.
- DELETE: `$app.delete()` Deletes an existing entity.

#### Request Handlers
Request handlers in Express PHP are strikingly similar to callbacks method, because they’re just functions (anonymous, named, or methods) with `$req` and `$res` parameters:
```php
$ping = function($req, $res) {
	echo 'ping';
};
$app->get('/', $ping);
```

## THE REQUEST OBJECT
The Express PHP Request object in short `$req` handles user's HTTP request.
Here the list of methods and properties of the Express PHP Request object that we’ll cover in this chapter:
- array `$request->query` query string parameters. GET HTTP request URL string queries
- array `$request->params` URL parameters
- array `$request->body` HTTP POST Request body data
- string `$request->route` the route path
- string `$request->method` the HTTP request method
- array `$request->cookies` cookie data
- array `$request->session` User SESSION data
- array `$request->header` HTTP request headers

### $request->query
The query string is everything to the right of the question mark in a given URL; for example, in the URL
_https://twitter.com/search?q=js&src=typd_, the query string is `q=js&src=typd`. After the query string is parsed by
Express PHP, the resulting PHP array would be `array('q'=>'js', 'src'=>'typd')`. This array is assigned to `$req->query` in your request handler, depending on what variable name you used in the function signature.
**Example:** Add the following Route to your `index.php`. Vist _http://express/?username=ahkohd&uid=1234_

```php
$app->get('/', function($req, $res){
	echo 'USERNAME: '.$req->query['username'].'<br/>';
	echo 'UID: '.$req->query['uid'].'<br/>';
});
```

**OUTPUT:** USERNAME: ahkohd
			UID: 1234

### $request->params
To experiment with the `$request->params` array, we can add a new route to our application.
This route will define URL parameters and print them in the console.
```php
$app->get('/params/[:role]/[:name]/[:status]', function($req, $res) {
	echo 'ROLE: '.$req->params['role'];
	echo 'NAME: '.$req->params['name'];
	echo 'STATUS: '.$req->params['status'];
});
```
Next, run the following URL on your browser _http://localhost/express/params/admin/azat/active_, _http://localhost/express/params/user/bob/active_

**OUTPUT:**
ROLE: admin		| ROLE: user
NAME: azat		| NAME: bob
STATUS: active  | STATUS: active

### $request->body
The `$request->body` array is another magical object that’s provided to us by Express PHP. It’s implemented by applying
the $_POST. The `$request->body` is used the get HTTP POST body data i.e When a user submit form.
To experiment with the `$request->body` array, we can add a new route to our application.
```php
$app->get('/', function($req, $res){
    $form = '<form method="POST" action="'.$GLOBALS['app']->get('basePath').'/">'
            . '<input type="text" name="username" placeholder="Enter your Username"/><br/>'
            .'<input type="email" name="email" placeholder="Enter your Email"/><br/>'
            . '</form>';
    // Send the HTML form we created
   $res->send($form, array(
      'Content-type'=>'text/html' 
   )); 
});

// CREATE A POST ROUTE TO PROCESS FORM
$app->post('/', function($req, $res){
	$res->send('USERNAME: '.$req->body['username'].'<br/>'.'EMAIL: '.$req->body['email'], array(
	'Content-type'=>'text/plain'
	));
});

```
Next, run the following URL on your browser _http://localhost/express/_ fill the form and see the result.
**OUTPUT:**
USERNAME: Ahkohd
Email: victor.olorunbunmi@gmail.com

### $request->route
Returns the current HTTP REQUEST URI
```php
$app->get('/profile', function($req, $res){
	echo 'REQUEST URI: '.$req->route;
});
```

Next, run the following URL on your browser _http://localhost/express/profile_
**OUTPUT:**
REQUEST URI: /express/profile

#### Match Types
You can use the following limits on your named parameters. The Router will create the correct regexes for you:
- `*` Match all request URIs
- `[i]` Match an integer
- `[i:id]` Match an integer as 'id'
- `[a:action]` Match alphanumeric characters as 'action'
- `[h:key]` Match hexadecimal characters as 'key'
- `[:action]` Match anything up to the next or end of the URI as 'action'
- `[create|edit:action]` Match either 'create' or 'edit' as 'action'
- `[*]` Catch all (lazy, stops at the next trailing slash)
- `[*:trailing]` Catch all as 'trailing' (lazy)
- `[**:trailing]` Catch all progressive - will match the rest of the URI
- `.[:format]?` Match an optional parameter 'format' a/or. before the block is also optional


### $request>method
Returns the current HTTP REQUEST METHOD
```php
$app->get('/', function($req, $res){
	echo 'REQUEST METHOD: '.$req->method;
});
```
Next, run the following URL on your browser _http://localhost/express/_
**OUTPUT:**
REQUEST METHOD: GET

### $request->cookies
Allows us to access HTTP REQUEST COOKIES (user-agent cookies). Cookies are automatically presented as a PHP array.
Let's experiment with `$req->cookies`. Set a new cookie using `$res->setCookie()` to set HTTP REQUEST COOKIES and 
use `$req->cookies` to access the COOKIES.
```php
$app->get('/cookies', function($req, $res){
	// Set COOKIES
	$res->setCookie('username', 'ahkohd', time() + (86400 * 30));
	$res->setCookie('uid', '1234', time() + (86400 * 30));
	echo 'Cookies SET!';
});

$app->get('/readCookies', function($req, $res){
	echo 'USERNAME COOKIE: '.$req->cookies['username'];
	echo 'UID COOKIE: '.$req->cookies['uid'];
});
```
Next, run the following URL on your browser _http://localhost/express/cookies_
**OUTPUT:** Cookies SET!
Then, run the following URL on your browser _http://localhost/express/readcookies_
**OUTPUT:**
USERNAME COOKIE: ahkohd
UID COOKIE: 1234

### $request->session
Allows us to access user's SESSION data. In other to enable/initiate user session you use the general middleware `$app->use(session_start())`.
**NOTE:** If users session is not initiated with `$app->use(session_start())` and you try to access `$req->session` it will
return _NULL_ as value.
Let's experiment with `$req->session`.
```php
// Initiate user session.
$app->use(session_start());
$app->get('/setSession', function($req, $res){
	// Use the `$res->setSession()` to set user SESSION
	$res->setSession('username', 'ahkohd');
	$res->setSession('uid', '1234');
	echo 'User session set!';
});

$app->get('/readSession', function($req, $res){
	echo 'Username SESSION DATA: '.$req->session['username'];
	echo 'UID SESSION DATA: '.$req->session['uid'];
});
```
Next, run the following URL on your browser _http://localhost/express/setSession_
**OUTPUT:** User session set!
Then, run the following URL on your browser _http://localhost/express/readSession_
**OUTPUT:**
Username SESSION DATA: ahkohd
UID SESSION DATA: 1234

### $request->header
Allows us to access list of HTTP REQUEST HEADER. It parses list of HTTP REQUEST header into an array.
Let's experiment with `$req->header`.
```php
header('Content-type:text/plain');
$app->get('/', function($req, $res){
	echo 'Content type Header: '.$req->header['Content-type'];
});

Next, run the following URL on your browser _http://localhost/express/_
**OUTPUT:**
Content type: text/plain


## THE RESPONSE OBJECT
The Express PHP response object (`$res` for short)— which is an argument in the request handler callbacks, is used to handle response to a request.
In this section, we’ll cover the following methods and attributes of the Express PHP response object in great detail:
- `$response->render()`
- `$response->set()`
- `$response->status()`
- `$response->send()`
- `$response->setCookie()`
- `$response->setSession()`
- `$response->redirect()`

### $response->render()
`$response->render()` is used to render a view template as a response to a request. The `$response->render(name, [data,])` method takes two parameters, the first parameter: name, which is the template name in a string format. The other parameters are data and callback.
To illustrate the most straightforward use case for `$response->render()`, we’ll create a page that shows a heading and a paragraph from a Smarty template. First, add a route. Here is an example of a simple setup for the home page route in the index.php file:
```php
// Set view engine to smarty
$app->set('view engine', 'Smarty');

// create a route and render a template;
$app->get('/render', function($req, $res){
	$res->render('render', array());
});
```

Then, add a new `views/render.tpl` file that looks static for now (i.e., it has no variables or logic):
```html
<h1> Express PHP </h1>
<p> Welcome to the Express PHP Response example!</p>
```
Finally, go to http://localhost/express/render in a browser.

In addition to the mandatory name parameter, `$response->render()`, has one required parameter: `data`. The data parameter makes templates more dynamic than static HTML files and allows us to update the output. For example, we can pass `title` to overwrite the value in the default value:
```php
// Set view engine to smarty
$app->set('view engine', 'Smarty');

// create a route and render a template;
$app->get('/render', function($req, $res){
	$res->render('render', array(
		'title'=>'Express PHP Render Data Example'
	));
});
```

Then,
```html
<h1> {$title} </h1>
<p> Welcome to the {$title}!</p>
```

Note: You can also render PHP files as view template. By default, Express PHP requires no template engine to render view, it can render, PHP file a template. Lets see an example:
```php
// Using no view engine
$app->set('view engine', 'default');

// create a route and render a template;
$app->get('/render', function($req, $res){
	$res->render('render', array(
		'title'=>'Express PHP Render Data Example'
	));
});
```

Then, add a new `views/render.php` file.
```php
<h1> <?php echo $data['title']?> </h1>
<p> Welcome to the <?php echo $data['title']?>!</p>
```
>Caution: The properties of the data parameter are your locals in the template. In other words, if you want to access
>a value of a title inside of your template, the data array must contain a key/value pair. Nested arrays are supported by
>most of the template engines.


### $response->set()
The `$response->set(field, [value])` method is used to set HTTP Header.
Here is an example from `index.php` of setting a single `Content-Type` response header to `text/html` and then
sending some simple HTML to the client:
```php
$app->get('/set-html', function($req, $res){
	$res->set('Content-Type', 'text/html');
	echo '<h1> Hello Express PHP';
});
```
You can see the results in the Network tab of Chrome Developer Tools, under the Headers subtab, which says
`Content-Type: text/html`. If we didn’t have `$response->set()` with `text/html`, then the response
would still have the HTML, but without the header. Feel free to comment the `$response->set()` and see it for yourself.

### $response->status()
The `$response->status()` method accepts an HTTP status code number and sends it in response. The most common
HTTP status codes are:
- 200: OK
- 201: Created
- 301: Moved Permanently
- 401: Unauthorized
- 404: Not Found
- 500: Internal Server 

Let's create a custom 404 page. Here we set the HTTP response Code to 404 - Not Found
```php
$app->get('/404', function($req, $res){
  $res->status(404);
  $res->send('<h1>Page Not Found</h1>', array(
    'Content-Type' => 'text/html'
    ));
  
});
```

### $response->send()
`$response->send()` is used to echo a response to the user. It is simply wraps PHP `echo` statement, but in this context it is more suitable for sending response as the second parameter lets you set array of HTTP Headers you want to send.
`$response->send([content], [headers,]);`
Let's play:
```php
$app->get('/send', function($req, $res){
  $res->send('This is a text file sent by the server.', array(
    'Content-Type' => 'text/plain'
    ));
  
});
```
When you browse the route on your browser:
**RESPONSE:**
>This is text file sent by the server

**Note:** The content type sent by the server is `text/plain`

### $response->setCookie()
The `$response->setCookie()` method is used to set Cookies. It is just a simple wrapper method of the setCookie() method.
Let's take a look of an example on how to use `$response->setCookie()`:
```php
$app->get('/set-cookies', function($req, $res){

  // Set COOKIES
  $res->setCookie('username', 'ahkohd', time() + (86400 * 30));
  $res->setCookie('uid', '1234', time() + (86400 * 30));
  echo 'Cookies SET!';
});

$app->get('/read-cookies', function($req, $res){

  // Read Cookies
  echo 'USERNAME COOKIE: '.$req->cookies['username'];
  echo 'UID COOKIE: '.$req->cookies['uid'];
});
```
Launch _http://localhost/express/set-cookies_ to set the COOKIES.
**OUTPUT:**
Cookies SET!
Browse _http://localhost/express/read-cookies_ to read the COOKIES you've set.
**OUTPUT:**
USERNAME COOKIES: ahkohd UID COOKIE: 1234

### $response->setSession()
`$response->setSession()` allows you to set user SESSION data. In other to enable/initiate user session you use the general middleware `$app->use(session_start())`.

Let's experiment with `$req->session`.
```php
// Initiate user session.
$app->use(session_start());
$app->get('/setSession', function($req, $res){
	// Use the `$res->setSession()` to set user SESSION
	$res->setSession('username', 'ahkohd');
	$res->setSession('uid', '1234');
	echo 'User session set!';
});

$app->get('/readSession', function($req, $res){
	echo 'Username SESSION DATA: '.$req->session['username'];
	echo 'UID SESSION DATA: '.$req->session['uid'];
});
```
Next, run the following URL on your browser _http://localhost/express/setSession_
**OUTPUT:** User session set!
Then, run the following URL on your browser _http://localhost/express/readSession_
**OUTPUT:**
Username SESSION DATA: ahkohd
UID SESSION DATA: 1234

### $res->redirect()
Allows you to redirect a the browser to another route.
**Example:** The following example will redirect you to the homepage, when you browse _http://localhost/express/redirect_
```php
$app->get('/redirect', function($req, $res){
  // Redirect to home page
  $res->redirect('/');
});
```

## Handling HTTP response errors
### Handling 404 error
In other to handle a 404 error; Express PHP by default, has a 404 error handler to handle 404 error. When a 404 error is found, Express PHP will out an error in the following format: `Cannot {$_SERVER['REQUEST_METHOD']} {$basePath} {$route} `
For example: When I browse a route that is not defined i.e `/undefined-route`, the custom error handler will return: `Cannot GET /undefined-route`.
However, this might not be preferred for a production environment. Express PHP allow you to set your own custom error handler. To do this, simply, set Express PHP `error 404` configuration option, to the name of a route that you want to dedicate for handling 404 error
The configuration setting looks like this:
```php
/**
 * Set Route to handle 404 error
 */
$app->set('error 404', 'name-of-dedicated-route-to-handler-404-errors');
```
Let's take a look at an example. Here I want route `/404` to be the 404 error customized handler:
```php
/**
 * Set Route to handle 404 error
 */
$app->set('error 404', '/404');
``` 
Then you define the route. Express PHP will then use the route's request handler to handle the 404 error.
```
$app->get('/404', function($req, $res){
  $res->status(404);
  $res->send('<h1>My Customized 404 Error Handler - Page Not Found</h1>', array(
    'Content-Type' => 'text/html'
    ));
  
});
```
To test it out; visit a route that is not defined. For example _http://localhost/express/undefinedRoute_
**OUTPUT:**
#### My Customized 404 Error Handler - Page Not Found



### Sharing Global Variables and Data Among Views
In other to share common data or global variables among views use `$app->setGlobal()`; The `$app->setGlobal()` let you 
set variables or data which are persistent among views. 

index.php
```
$app->set('view engine', 'default')

// syntax: $app->setGlobal('key', 'value');

$app->setGlobal('appName', 'Express App');

$app->get('/', funtion($req, $res){
    $res->render('home', array(
        'title' => 'HomePage'
    ));
});


$app->get('/about', funtion($req, $res){
    $res->render('about', array(
        'title1' => 'About Page'
    ));
});


```

home.php
```
...
<title><?php $data['appName'].' / '.$data['title']; ?> </title>
...
```



about.php
```
...
<title><?php $data['appName'].' / '.$data['title1']; ?> </title>
...
```

# License
[The MIT License](LICENSE.md)
