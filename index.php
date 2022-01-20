<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 1/19/2022
 * Time: 12:28 PM
 */

include "api_responses/Response.php";
include "api_responses/Request.php";
include "controllers/ApplicationLogicController.php";
include "environment/LoadEnvironmentVariables.php";
include "database/DatabaseConnection.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

(new LoadEnvironmentVariables(__DIR__ . '/.env'))->load();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$requestMethod = $_SERVER["REQUEST_METHOD"];

if (count($uri) < 5) {
    echo json_encode(new Response(412, "Request Path is Incorrect"));
    exit();
}

//split the path into some useful components
$context = $uri[1];
$file = $uri[2];
$controller = $uri[3];
$action = $uri[4];
$payload = json_decode(file_get_contents('php://input'), true);


if ($controller != "code-generation") {
    echo json_encode(new Response(404, "Unknown Operation"));

} elseif ($action == null) {
    echo json_encode(new Response(412, "Please Specify Required Action "));

} else {
    //now route the traffic
    $validRoute = validateTrafficRoute($action);

    if (!$validRoute) {
        echo json_encode(new Response(404, "Unknown Route"));

    } else {
        //get api response from the controller logic
        $apiResponse = routeTraffic($action, $payload);
        echo json_encode($apiResponse);
    }

}

//route traffic to the controller
function routeTraffic($action, $payload)
{

    $response = new Response(500, "Internal Server Error Occured");
    $app = new ApplicationLogicController();
    $request=convertJsonToPojo($payload);

    if ($action === "generate") {
        $response = $app->generatePromoCodes($request);

    } elseif ($action === "deactivate") {
        $response = $app->deactivatePromoCode($request);

    } elseif ($action === "list_active_codes") {
        $response = $app->listActivePromoCodes($request);

    } elseif ($action === "list_all_codes") {
        $response = $app->listAllPromoCodes($request);

    } elseif ($action === "redeem_code") {
        $response = $app->redeemCode($request);

    } elseif ($action === "validate") {
        $response = $app->validatePromoCode($request);
    }

    return $response;
}


//confirm the action is among the list of actions known to the application
function validateTrafficRoute($action)
{
    $actions = ["generate", "deactivate", "list_all_codes", "list_active_codes", "validate", "redeem_code"];
    if (!in_array($action, $actions)) {
        return false;
    } else {
        return true;
    }
}


function convertJsonToPojo($payload)
{
    $request = new Request();
    foreach ($payload as $key => $value) {
        $request->set($key,$value);
    }

    return $request;
}
