<?php
require '/Applications/MAMP/htdocs/FIDO_SERVER/SendChallenge.php';
$filePath = '/Applications/MAMP/htdocs/FIDO_SERVER/challenge.txt';

$p_id = isset($_POST["p_id"]) ? $_POST["p_id"] : "";

class Version {
    public $major = 1;
    public $minor = 1;
}

abstract class Operation {
    const Reg = 1;
    const Auth = 2;
    const Dereg = 3;
}

class OperationHeader {
    public $upv;
    public $op;
    public $appID="bpm";  //이건 client쪽에서 생성해서 server가 정당한 facetid를 주는것
    public $serverdata;
    // extension은 비움    
    
    public function __construct() {
        $this->upv = new Version();
        $this->op = Operation::Auth;
        $this->serverdata = "1440";  //session expire time
    }
}

class MatchCriteria{
    public $userVerification = "1023";
    public $authenticationAlgorithms = "RSA";
    public $assertionSchemes = "UAFV1TLV";
}

//FIDO의 product_list조회
function getTx($p_id) {
    $con = mysqli_connect("192.168.0.2", "root", "root", "bpm", 3306);
    mysqli_query($con, 'SET NAMES utf8');

    // SQL 문을 실행하여 pk 테이블에서 publickey를 조회합니다.
    $sql = "SELECT * FROM product_list WHERE p_id = '$p_id'";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        // 결과가 있을 경우 publickey 값을 반환합니다.
        $row = $result->fetch_assoc();
        $filteredRow = array_diff_key($row, ['p_id' => true]);
        return $filteredRow;
    } else {
        // 결과가 없을 경우 0을 반환합니다.
        return "0";
    }
}

class BuyRequest{
    public $header;
    public $challenge;
    public $username;
    public $policy;
    public $transac;
    
    public function __construct() {
        $this->header = new OperationHeader();
        $this->challenge =  createChallenge();// Set default value to Reg
        $this->username = isset($_POST["userID"]) ? $_POST["userID"] : "";
        $this->policy = new MatchCriteria();

        $p_id = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
        $this->transac = getTx($p_id);
    }
}

$buy = new BuyRequest();
// echo $regi->policy->userVerification;
$data = $buy->transac;
unset($data['p_left']);
$data['p_num'] = 1;

$sn = array("challenge"=>json_encode($buy->challenge),"transaction"=>json_encode($data));
file_put_contents($filePath, json_encode($sn));

header('Content-Type: application/json');
header('FIDO-Major-Version: ' . $buy->header->upv->major);
header('FIDO-Minor-Version: ' . $buy->header->upv->minor);
header('FIDO-Operation: ' . $buy->header->op);
header('FIDO-appID: '.$buy->header->appID);
header('FIDO-serverdata: '.$buy->header->serverdata);

$response = array(
    'Username' => $buy->username,
    'Challenge' => $buy->challenge,
    'Policy' => $buy->policy,
    'Transaction' => $data
);

echo json_encode($response);

?>