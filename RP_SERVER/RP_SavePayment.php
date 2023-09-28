<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

try {
    $db = new PDO("mysql:host=192.168.0.2;dbname=bpm;charset=utf8", "root", "root");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sanitize the user ID input
    $userID = isset($_POST["userID"]) ? $_POST["userID"] : "";
    $product = isset($_POST["product"]) ? $_POST["product"] : "";
    $unitPrice = isset($_POST["unitPrice"]) ? $_POST["unitPrice"] : "";
    $unitPrice = intval($unitPrice); //1개 가격

    // Use prepared statements to prevent SQL injection
    $statement = $db->prepare("SELECT amount FROM PAY_DETAIL WHERE product = :product AND userID = :userID");
    $statement->bindParam(':product', $product, PDO::PARAM_STR);
    $statement->bindParam(':userID', $userID, PDO::PARAM_STR);
    $statement->execute();

    $response = array();
    $response["success"] = false;

    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $bfAmount = intval($row['amount']);
        $atAmount = $bfAmount + 1;
        $totalPrice = $atAmount * $unitPrice;

        // Use another prepared statement for the UPDATE query
        $statement2 = $db->prepare("UPDATE PAY_DETAIL SET amount = :atAmount, totalPrice = :totalPrice WHERE product = :product AND userID = :userID");
        $statement2->bindParam(':product', $product, PDO::PARAM_STR);
        $statement2->bindParam(':userID', $userID, PDO::PARAM_STR);
        $statement2->bindParam(':atAmount', $atAmount, PDO::PARAM_INT);
        $statement2->bindParam(':totalPrice', $totalPrice, PDO::PARAM_INT);

        if ($statement2->execute()) {
            $response["success"] = true;
        }
    }

    echo json_encode($response);
} catch (PDOException $e) {
    // 에러 처리
    echo "Database Error: " . $e->getMessage();
}
?>