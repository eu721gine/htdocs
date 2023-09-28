<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {  

    $db = new PDO("mysql:host=192.168.0.2;dbname=bpm;charset=utf8", "root", "root");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $userID = isset($_POST["userID"]) ? $_POST["userID"] : "";

    // 카드 등록 여부 확인
    $query = "SELECT COUNT(*) AS cardCount FROM card WHERE userID = :userID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $response = array();
    if($result["cardCount"] > 0){
        $response["isCardRegistered"] = true;
    }else{
        $response["isCardRegistered"] = false;   
    }
    echo json_encode($response);

} catch (PDOException $e) {
    // 데이터베이스 연결 오류 또는 쿼리 실행 오류 처리
    $errorResponse = array(
        "error" => "Database Error",
        "message" => $e->getMessage()
    );
    echo json_encode($errorResponse);
}
?>

