<?php

/*
 *   _____                                ____  _   _ ____
 *  | ____|_  ___ __  _ __ ___  ___ ___  |  _ \| | | |  _ \
 *  |  _| \ \/ / '_ \| '__/ _ \/ __/ __| | |_) | |_| | |_) |
 *  | |___ >  <| |_) | | |  __/\__ \__ \ |  __/|  _  |  __/
 *  |_____/_/\_\ .__/|_|  \___||___/___/ |_|   |_| |_|_|   v 1.0.0
 *             |_|
 *
*/

# Require Express PHP Framework...
require_once 'Express.php';

# Create an Expess PHP app...
global $app;
$app = new Express();

# Require Configuration file...
require_once "config.php";

# Define app routes... 
require_once "routes/Apis.php";
require_once "routes/Web.php";