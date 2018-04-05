<?php


// GET HTTP REQUEST

$app->get('/', function($req, $res) {
	$res->render('home', array('title'=>'Home'));
});