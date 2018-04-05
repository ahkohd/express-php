<?php

# Require ExpressORM...
require 'ExpressORM.php';


$export = function(){

    # Instance the module...
    $module = new ExpressORM();

    # Inject Module into Express...
    $GLOBALS['app']->_ExpressORM = $module;
};

# Export module..
$export();