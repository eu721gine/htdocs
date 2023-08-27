<?php
//session_start();

$filePath = '/Applications/MAMP/htdocs/challenge.txt';
$chall = file_get_contents($filePath);


error_reporting(E_ALL);
ini_set('display_errors', 1);

$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


function getPublicKey($userID) {
    $con = mysqli_connect("192.168.0.2", "root", "root", "bpm", 3306);
    mysqli_query($con, 'SET NAMES utf8');

    // SQL 문을 실행하여 pk 테이블에서 publickey를 조회합니다.
    $sql = "SELECT publicKey FROM PK WHERE userID = '$userID'";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        // 결과가 있을 경우 publickey 값을 반환합니다.
        $row = $result->fetch_assoc();
        return $row['publicKey'];
    } else {
        // 결과가 없을 경우 0을 반환합니다.
        return "0";
    }
}

$userID = isset($_POST['userID']) ? $_POST["userID"] : "";
$message = isset($_POST['message']) ? $_POST["message"] : "";
$signature = isset($_POST['signature']) ? $_POST["signature"] : "";
$publicKey1 = isset($_POST['publicKey']) ? $_POST["publicKey"] : "";

$publicKey = getPublicKey($userID);

$issame = strcmp($publicKey1, $publicKey);

if ($issame === 0) {
    $issame2 = true;
} else {
    $issame2 = false;
}

//공개키 유효성 검사
function isValidPublicKey($publicKey)
{
    $decodedPublicKey = base64_decode($publicKey);
    if ($decodedPublicKey === false) {
        return false; // base64 디코딩 실패
    }

    $pemPublicKey = "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($decodedPublicKey), 64, "\n") . "-----END PUBLIC KEY-----";
    $publicKeyResource = openssl_pkey_get_public($pemPublicKey);
    if ($publicKeyResource === false) {
        return false; // 유효하지 않은 PEM 형식
    }

    // 공개 키 검증 성공
    return true;
}

$isPublicKeyValid = isValidPublicKey($publicKey);


$decodedPublicKey = base64_decode($publicKey);
if ($decodedPublicKey !== false) {
    $pemPublicKey = "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($decodedPublicKey), 64, "\n") . "-----END PUBLIC KEY-----";
    $publicKeyResource = openssl_pkey_get_public($pemPublicKey);
} else {
    $publicKeyResource = false;
}

$response = array();
$response["success"] = false;
//$response["isValidPublicKey"] = $isPublicKeyValid;
//$response["issame"] = $issame2;


if ($publicKeyResource !== false) {
    $verify = openssl_verify($chall, base64_decode($signature), $publicKeyResource, OPENSSL_ALGO_SHA256);
    if ($verify === 1) {
        $response["success"] = true;
    }
}

echo json_encode($response);
?>
