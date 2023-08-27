<?php
$targetUrl = 'https://192.168.0.5:443/test.php'; // POST 요청을 보낼 대상 URL

$data = array(
    'key1' => 'value1',
    'key2' => 'value2'
); // 보낼 데이터

$options = array(
    CURLOPT_URL => $targetUrl,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
);

$ch = curl_init($targetUrl);

curl_setopt_array($ch, $options);

$response = curl_exec($ch);

if ($response === false) {
    echo "cURL error: " . curl_error($ch);
} else {
    echo "Response from target server: " . $response . "\n";
}

curl_close($ch);

?>