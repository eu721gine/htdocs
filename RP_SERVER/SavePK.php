<?php 
    $filePath = '/Applications/MAMP/htdocs/FIDO_SERVER/challenge.txt';
    $chall = file_get_contents($filePath);

    error_reporting(E_ALL); 
    ini_set('display_errors',1); 

    $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");

    $con = mysqli_connect("192.168.0.2", "root", "root", "bpm", 3306);
    mysqli_query($con,'SET NAMES utf8');
    
    $userID = isset($_POST["userID"]) ? $_POST["userID"] : "";
    $publicKey = isset($_POST["publicKey"]) ? $_POST["publicKey"] : "";
    $signature = isset($_POST["signedChallenge"]) ? $_POST["signedChallenge"] : "";

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

    $verify = openssl_verify(hash('sha256', $chall, true), base64_decode($signature), $publicKeyResource, OPENSSL_ALGO_SHA256);
    
    if ($verify === 1) {

        $statement = mysqli_prepare($con, "INSERT INTO PK VALUES (?,?)");
        mysqli_stmt_bind_param($statement, "ss", $userID, $publicKey);
        mysqli_stmt_execute($statement);

        $response["success"] = true;
        echo json_encode($response);

    }else{
        $response["success"] = false;
        echo json_encode($response);
    }
 
?>