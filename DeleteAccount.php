<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $db = new PDO("mysql:host=192.168.0.2;dbname=bpm;charset=utf8", "root", "root");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 사용자 입력
    $userIDToDelete = isset($_POST["userID"]) ? $_POST["userID"] : "";

    if ($userIDToDelete != "") {
        // SQL 쿼리를 바인딩하여 사용자 계정 정보 삭제 처리
        $statement = $db->prepare("DELETE FROM users WHERE userID = :userIDToDelete");

        // 바인딩
        $statement->bindParam(':userIDToDelete', $userIDToDelete, PDO::PARAM_STR);

        // 쿼리 실행
        $result = $statement->execute();

        // 사용자 공개키 정보 삭제 처리
        $keyStatement = $db->prepare("DELETE FROM pk WHERE userID = :userIDToDelete");

        // 바인딩
        $keyStatement->bindParam(':userIDToDelete', $userIDToDelete, PDO::PARAM_STR);

        // 공개키 정보 쿼리 실행
        $keyResult = $keyStatement->execute();

        $response = array();
        if ($result && $keyResult) {
            $response["success"] = true;
        } else {
            $response["success"] = false;
        }
    } else {
        $response = array();
        $response["success"] = false;
    }

    echo json_encode($response);
} catch (PDOException $e) {
    // 에러 처리
    echo "Database Error: " . $e->getMessage();
}
?>