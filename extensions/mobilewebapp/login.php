<?php

       include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."cometchat_init.php");

       if(!empty($_REQUEST['username']) && !empty($_REQUEST['password']) && $_REQUEST['password']!= '' && $_REQUEST['username']!= '' ) {
               echo chatLogin($_REQUEST['username'],$_REQUEST['password']);
       }
?>