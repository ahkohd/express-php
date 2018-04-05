<?php

# Configure Express PHP app...

		/**
		 * Set Express router's base path
		 */
		 $app->set('basePath', '/express-php-master');
		 
		 /**
		  * Set Express view engine
		  */
		 $app->set('view engine', 'default');

		 /**
		  * Set Express views path
		  */
		$app->set('views', 'views/');

		/**
		  * Set Express static files path
		  */
		$app->set('static', 'public/');


		/**
		 *  Set App Global variable
		 */


		$app->setGlobal('appName', 'Express App');


		/**
		 * Inject the app object into the view.
		 */

		$app->setGlobal('app', $app);

		/*
		 * Import Express Modules
		 */


		# Import database module...
		$app->import('ExpressORM');



		/**
		 * Connect to database
		 */

		$app->_ExpressORM->Instance('localhost', 'root', '', 'revo');
		if(!$app->_ExpressORM->CheckInstance()) throw new Exception("Database Error: Unable to Connect to Database");


