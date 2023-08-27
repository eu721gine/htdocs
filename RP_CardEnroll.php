<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to allow only alphanumeric characters and Korean characters
function only_number(String $content){
    return preg_replace('/[^0-9]/','', $content);
}

try {
    $db = new PDO("mysql:host=192.168.0.2;dbname=bpm;charset=utf8", "root", "root");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 사용자 입력
    $userID = isset($_POST["userID"]) ? $_POST["userID"] : "";
    $c_num = isset($_POST["c_num"]) ? only_number($_POST["c_num"]) : "";
    $c_cvc = isset($_POST["c_cvc"]) ? only_number($_POST["c_cvc"]) : "";
    $c_date = isset($_POST["c_date"]) ? only_number($_POST["c_date"]) : "";
    $c_pw = isset($_POST["c_pw"]) ? preg_replace('/[^0-9]/', '', $_POST["c_pw"]) : "";

    // SQL 쿼리를 바인딩하여 사용자 입력 처리
    $statement = $db->prepare("INSERT INTO card (userID, c_num, c_cvc, c_date, c_pw) VALUES (:userID ,:c_num, :c_cvc, :c_date, :c_pw)");

    // 바인딩
    $statement->bindParam(':userID', $userID, PDO::PARAM_STR);
    $statement->bindParam(':c_num', $c_num, PDO::PARAM_STR);
    $statement->bindParam(':c_cvc', $c_cvc, PDO::PARAM_STR);
    $statement->bindParam(':c_date', $c_date, PDO::PARAM_STR);
    $statement->bindParam(':c_pw', $c_pw, PDO::PARAM_INT);

    // 쿼리 실행
    $statement->execute();

    $response = array();
    $response["success"] = true;

    echo json_encode($response);
} catch (PDOException $e) {
    // 에러 처리
    echo "Database Error: " . $e->getMessage();
}
?>