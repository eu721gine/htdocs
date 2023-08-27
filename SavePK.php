<?php 
    error_reporting(E_ALL); 
    ini_set('display_errors',1); 

    $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");



    $con = mysqli_connect("192.168.0.2", "root", "root", "bpm", 3306);
    mysqli_query($con,'SET NAMES utf8');
    
    $userID = isset($_POST["userID"]) ? $_POST["userID"] : "";
    $publicKey = isset($_POST["publicKey"]) ? $_POST["publicKey"] : "";

    $statement = mysqli_prepare($con, "INSERT INTO PK VALUES (?,?)");
    mysqli_stmt_bind_param($statement, "ss", $userID, $publicKey);
    mysqli_stmt_execute($statement);


    $response = array();
    $response["success"] = true;
 
   
    echo json_encode($response);

?>