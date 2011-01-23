<?php
    session_start();

    include_once("classError.php");
    include_once("classDBmysql.php");
    include_once("classAuth.php");

    $error = new Error();
    $db = new DBmysql($error);
    $db2 = new DBmysql($error);
    $auth = new Auth($db, $error, $username, $password);
?>