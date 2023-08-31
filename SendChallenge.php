<?php 

//챌린지저장 디렉토리
$filePath = '/Applications/MAMP/htdocs/challenge.txt';

// 클라이언트로부터의 요청을 확인합니다.
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 챌린지 생성 함수를 호출하여 챌린지를 생성합니다.
    $challenge = createChallenge();
    //$_SESSION['challenge'] = ($challenge['challenge']);
    file_put_contents($filePath, json_encode($challenge));
    // 생성된 챌린지를 클라이언트에게 반환합니다.
    echo json_encode($challenge);
}

/**
 * 챌린지 생성 함수
 *
 * 이 함수는 FIDO UAF 프로토콜 버전 1.1에 명시된 규칙에 따라 챌린지를 생성합니다.
 * 실제로는 FIDO UAF 사양을 준수하는 적절한 암호화 라이브러리를 사용해야 합니다.
 *
 * @return array 생성된 챌린지 데이터
 */
function createChallenge() {
    // 임의의 챌린지 값을 생성합니다.
    $challenge = generateRandomValue();
    
    // 챌린지 데이터를 구성합니다.
    $challengeData = array(
        'challenge' => $challenge,
        'version' => '1.1', // FIDO UAF 프로토콜 버전
        // 기타 필요한 챌린지 데이터 (프로토콜 사양에 따라 구성)
    );

    return $challengeData;
}

/**
 * 임의의 값 생성 함수
 *
 * 이 함수는 임의의 값을 생성하여 반환합니다.
 * 이 예시에서는 챌린지 생성을 위해 사용됩니다.
 * 실제로는 FIDO UAF 사양에 명시된 암호화 요구사항에 따라 적절한 암호화 함수를 사용해야 합니다.
 *
 * @return string 생성된 임의의 값
 */
function generateRandomValue() {
    $length = 32; // 생성할 임의의 값의 길이
    $bytes = openssl_random_pseudo_bytes($length, $strong);
    if ($strong === true) {
        return bin2hex($bytes);
    } else {
        // 암호적으로 강력한 난수를 생성하지 못한 경우에 대한 예외 처리
        // 적절한 오류 처리를 수행해야 합니다.
        throw new Exception('Failed to generate strong random value.');
    }
}

?>

