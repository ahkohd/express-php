<?php

# Require Express Validator Class Definition...
require 'ExpressValidator.php';


$export = function(){
	
	# Instance the module...
	$module = new ExpressValidator();

	# Inject Module into Express...
	$GLOBALS['app']->_ExpressValidator = $module;
};

# Export module..
$export();