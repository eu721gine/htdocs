<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// POST로 전달된 데이터 받기
$userID = isset($_POST['userID']) ? $_POST['userID'] : '';

try {
    // Create a new PDO connection
    $db = new PDO("mysql:host=192.168.0.2;dbname=bpm;charset=utf8", "root", "root");

    // Set PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the DELETE query using a parameterized statement
    $statement = $db->prepare("DELETE FROM PK WHERE userID = :userID");
    $statement->bindParam(':userID', $userID, PDO::PARAM_STR);

    // Execute the DELETE query
    $result = $statement->execute();

    // 응답 데이터 생성
    $response = array();
    if ($result) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
    }

    // 응답 데이터 전송
    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    // 에러 처리
    echo "Database Error: " . $e->getMessage();
}
?>