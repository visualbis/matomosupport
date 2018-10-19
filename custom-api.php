<?php
require 'vendor/autoload.php';
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

error_reporting(E_ERROR | E_PARSE);

$host_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http").'://'.$_SERVER['HTTP_HOST'];
$client = new Client(['base_uri' => $host_url ]);

$file = file_get_contents('su_token.key');
$SU_TOKEN =  preg_replace( '/\s*/m', '',$file );

$userName = $_GET['userName'];
$password = $_GET['password'];
$siteName = $_GET['siteName'];

$defaultParams = '/?module=API&format=JSON';
$defaultParams = array(
    'module' => 'API',
    'format' => 'JSON'
);

// ---- Function for Matomo call -----//

function callMatomo($params,$userExistCall = false) {

    $response  = $GLOBALS['client']->get(dirname($_SERVER['PHP_SELF']),[
        'query' => $params
    ]);

    $body = $response->getBody();
    $json = json_decode($body);
    if($userExistCall && !property_exists($json, "value")){
        $res -> errorCode = 77;
        header('Content-Type: application/json');
        echo json_encode($res);
        exit();
    }
    if(!property_exists($json, "value")){
        header('Content-Type: application/json');
        echo $body;
        exit();
    }
    return $json;
}
// ---- Function for Matomo call end-----//

// ----------------------- Validating username and password ---------------------//
// ------------ Get User Token --------------//

$params = $defaultParams;
$params["method"]="UsersManager.getTokenAuth";
$params["userLogin"]=$userName;
$params["md5Password"]=$password;

$userToken = callMatomo($params) -> value;

// ------------ Get User Token end --------------//

// Note the Matomo API returns token even the password is incorrect, so we need to validate the token to verify the password.

// ------------ Validate User Token ---------//

$params = $defaultParams;
$params["method"]="UsersManager.userExists";
$params["userLogin"]=$userName;
$params["token_auth"]=$userToken;

callMatomo($params,true);

// ------------ Validate User Token end ---------//
// ----------------------- Validating username and password end ---------------------//

// ------------ Adding New Site ----------------//

$params = $defaultParams;
$params["method"]="SitesManager.addSite";
$params["siteName"]=$siteName;
$params["token_auth"]=$SU_TOKEN;

$siteId = callMatomo($params) -> value;

// ------------ Adding New Site end----------------//

// ------------ Set View Access for the site to the user ---------//

$params = $defaultParams;
$params["method"]="UsersManager.setUserAccess";
$params["userLogin"]=$userName;
$params["access"]="view";
$params["idSites"]=$siteId;
$params["token_auth"]=$SU_TOKEN;

$res = callMatomo($params);
header('Content-Type: application/json');
echo json_encode($res);

// ------------ Set View Access for the site to the user end ---------//