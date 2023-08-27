<?php
$targetUrl = 'https://192.168.0.5:443/test.php'; // POST 요청을 보낼 대상 URL

$dataToSend = "Hello from Sending Server!"; // 보낼 데이터

$ch = curl_init($targetUrl);

// SSL 인증서 검증 비활성화
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// POST 요청 설정
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $dataToSend);

// 응답을 받아오기 위해 설정
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if ($response === false) {
    echo "cURL error: " . curl_error($ch);
} else {
    echo "Response from target server: " . $response . "\n";
}

curl_close($ch);

?>