<?php
    error_reporting(E_ALL); 
    ini_set('display_errors',1); 

    $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");


    $con = mysqli_connect("192.168.0.2", "bpm", "root", "root", 3306);
    mysqli_query($con,'SET NAMES utf8');

    $userID = isset($_POST["userID"]) ? $_POST["userID"] : "";

    $sql = "SELECT publicKey FROM PK WHERE userID = '$userID' ";
    $result = $con->query($sql);

    if ($result->num_rows > 0){
       $row = $result->fetch_assoc();
       echo $row["publicKey"];
    } else {
       echo "0";
    }
    #$statement = mysqli_prepare($con, "SELECT * FROM PK WHERE userID = '$userID' ");
    #mysqli_stmt_bind_param($statement, "s", $publicKey);
    #mysqli_stmt_execute($statement);


    #mysqli_stmt_store_result($statement);
    #mysqli_stmt_bind_result($statement, $publicKey);

    #$response = array();
    #$response["success"] = false;
 
    #while(mysqli_stmt_fetch($statement)) {
     #   $response["success"] = true;
      #  $response["publicKey"] = $publicKey;	
    #}

    #echo json_encode($response);

?>

