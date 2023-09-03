<?php
require '/Applications/MAMP/htdocs/SendChallenge.php';
$filePath = '/Applications/MAMP/htdocs/challenge.txt';

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
    public $appID=null;  //이건 client쪽에서 생성해서 server가 정당한 facetid를 주는것
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
$sn = array("challenge"=>json_encode($buy->challenge),"transaction"=>json_encode($buy->transac));
file_put_contents($filePath, json_encode($sn));


// Usage example
// $header = new OperationHeader();
// $header->op = Operation::Reg;

// Access and print the values
// echo "Version: {$header->upv->major}.{$header->upv->minor}" . PHP_EOL;
// echo "Operation: {$header->op}" . PHP_EOL;
// echo "App ID: {$header->appID}" . PHP_EOL;

$response = array(
    'Header' => $buy->header,
    'Username' => $buy->username,
    'Challenge' => $buy->challenge,
    'Policy' => $buy->policy,
    'Transaction' => $buy->transac
);

echo json_encode($response);

?>