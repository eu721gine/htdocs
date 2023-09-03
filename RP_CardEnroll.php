<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to allow only alphanumeric characters and Korean characters
function only_number($content){
    return preg_replace('/[^0-9]/', '', $content);
}

try {
    $db = new PDO("mysql:host=192.168.0.2;dbname=bpm;charset=utf8", "root", "root");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // User input
    $userID = isset($_POST["userID"]) ? $_POST["userID"] : "";
    $c_num = isset($_POST["c_num"]) ? $_POST["c_num"] : "";
    $c_cvc = isset($_POST["c_cvc"]) ? $_POST["c_cvc"] : "";
    $c_date = isset($_POST["c_date"]) ? $_POST["c_date"] : "";
    $c_pw = isset($_POST["c_pw"]) ? $_POST["c_pw"] : "";

    // Process user input by binding SQL queries
    $statement = $db->prepare("INSERT INTO card (userID, c_num, c_cvc, c_date, c_pw) VALUES (:userID, :c_num, :c_cvc, :c_date, :c_pw)");

    // Binding
    $statement->bindParam(':userID', $userID, PDO::PARAM_STR);
    $statement->bindParam(':c_num', $c_num, PDO::PARAM_STR);
    $statement->bindParam(':c_cvc', $c_cvc, PDO::PARAM_STR);
    $statement->bindParam(':c_date', $c_date, PDO::PARAM_STR);
    $statement->bindParam(':c_pw', $c_pw, PDO::PARAM_STR); // Assuming the password is also encrypted

    // Execute query
    $statement->execute();

    $response = array();
    $response["success"] = true;

    echo json_encode($response);
} catch (PDOException $e) {
    // Error handling
    echo "Database Error: " . $e->getMessage();
}
?>