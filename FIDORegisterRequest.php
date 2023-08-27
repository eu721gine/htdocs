<?php
require '/Applications/MAMP/htdocs/SendChallenge.php';

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
        $this->op = Operation::Reg; // Set default value to Reg
        $this->serverdata = "1440";  //session expire time
    }
}

class MatchCriteria{
    public $userVerification = "1023";
}

class RegisterRequest{
    public $header;
    public $challenge;
    public $username;
    public $policy;
    
    public function __construct() {
        $this->header = new OperationHeader();
        $this->challenge =  createChallenge();// Set default value to Reg
        $this->username = isset($_POST["userID"]) ? only_alpha_number($_POST["userID"]) : "";
        $this->policy = new MatchCriteria();
    }
}

$regi = new RegisterRequest();
// echo $regi->policy->userVerification;

// Usage example
// $header = new OperationHeader();
// $header->op = Operation::Reg;

// Access and print the values
// echo "Version: {$header->upv->major}.{$header->upv->minor}" . PHP_EOL;
// echo "Operation: {$header->op}" . PHP_EOL;
// echo "App ID: {$header->appID}" . PHP_EOL;

$response = array(
    'Header' => $regi->header,
    'Username' => $regi->username,
    'Challenge' => $regi->challenge,
    'Policy' => $regi->policy
);

echo json_encode($response);

?>