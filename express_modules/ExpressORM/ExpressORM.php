<?php

# Require the MySQli Wrapper Class;
require_once("MysqliDb.php");

class ExpressORM
{
    public $Model;
    private  $connected = false;

    public function __construct()
    {

    }

    public function  Connect($host, $username, $password, $databaseName)
     {

     }

     function __call($name, $arguments)
     {
         if($name=="Instance") {

             if(count($arguments) == 4 && is_string($arguments[0]))
             {
                 // The default connection mode
                 if($this->Model = new MysqliDb ($arguments[0], $arguments[1], $arguments[2], $arguments[3]))
                 {
                     $this->connected = true;
                 }
             } else if(count($arguments) == 1 && is_array($arguments[0]))
             {
                 // Using advanced mode of connection with setting configuration with arrays.
                 if($this->Model = new MysqliDb($arguments[0]))
                 {
                     $this->connected = true;
                 }

             } else {
                 throw  new Exception("Instance Err: Invalid Arguments");
             }
         } else {
             throw  new Exception("Fatal Error: ".$name." is not a method of ".__CLASS__);
         }
     }

     /*
      * This method returns the connection state to the
      * database, if connected returns 'true', else
      * returns 'false'
      */

     public function CheckInstance()
     {
         return $this->connected;
     }
}