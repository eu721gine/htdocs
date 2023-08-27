<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

# include('android_log_php.php');

$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

// Function to allow only alphanumeric characters and Korean characters
function only_alpha_number(String $content){
  return preg_replace('/[^a-zA-Z0-9가-힣]/u', '', $content);
}

try {
    $db = new PDO("mysql:host=192.168.0.2;dbname=bpm;charset=utf8", "root", "root");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sanitize the user ID input
    $userID = isset($_POST["userID"]) ? only_alpha_number($_POST["userID"]) : "";
    $userPassword = isset($_POST["userPassword"]) ? $_POST["userPassword"] : "";

    $statement = $db->prepare("SELECT * FROM users WHERE userID = :userID AND userPassword = SHA1(:userPassword)");
    $statement->bindParam(':userID', $userID, PDO::PARAM_STR);
    $statement->bindParam(':userPassword', $userPassword, PDO::PARAM_STR);
    $statement->execute();

    $response = array();
    $response["success"] = false;

    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $response["success"] = true;
        $response["userID"] = $row["userID"];
        $response["userPassword"] = $row["userPassword"];
        $response["userName"] = $row["userName"];
        $response["userAge"] = $row["userAge"];
    }

    echo json_encode($response);
} catch (PDOException $e) {
    // 에러 처리
    echo "Database Error: " . $e->getMessage();
}
?>