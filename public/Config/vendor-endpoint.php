<?php

require_once 'lib.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'];

$pp = explode('/', $path);
$n = count($pp);
$appId = $pp[$n - 2];
$accountId = $pp[$n - 1];


$app = AppInstanceContoller::load($appId, $accountId);
$replyStatus = true;

switch ($method) {
    case 'PUT':
        $requestBody = file_get_contents('php://input');

        $data = json_decode($requestBody);

        $appUid = $data->appUid;
        if (property_exists($data, 'access')) $accessToken = $data->access[0]->access_token;
        else $accessToken = $app->TokenMoySklad;

        if (!$app->getStatusName()) {
            $app->TokenMoySklad = $accessToken;
            $app->status = AppInstanceContoller::SETTINGS_REQUIRED;
            $app->persist();

        }
        $url = 'https://smartrekassa.kz/setAttributes/' . $accountId . '/' . $accessToken;
        $install = file_get_contents($url);
        break;
    case 'GET':
        break;
    case 'DELETE':
        //Тут так же
        $url = 'https://smartrekassa.kz/delete/'.$accountId;
        $install = file_get_contents($url);

        $replyStatus = false;
        break;
}

if (!$app->getStatusName()) {
    http_response_code(404);
} else if ($replyStatus) {
    header("Content-Type: application/json");
    echo '{"status": "' . $app->getStatusName() . '"}';
}


