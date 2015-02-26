<?php
function __autoload($class_name)
 { 
 if($class_name=="Repository_Transaction"): $class_name="RepositoryTransaction"; endif;
 
 require_once "lib/".$class_name.'.class.php'; 
 }
 function load($class_name,$type)
 {
 	require_once $type."/".$class_name.".php";
 }
?>