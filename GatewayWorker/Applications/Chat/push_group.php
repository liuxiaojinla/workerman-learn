<?php
use GatewayWorker\Lib\Gateway;

require_once __DIR__.'/../../vendor/autoload.php';
require_once './lib.php';

$token = isset($_POST['token']) ? $_POST['token'] : '';
if(empty($token)) output_error('token invalid！');

$data = isset($_POST['data']) ? $_POST['data'] : '';
if(empty($data)) output_error('data invalid！');

$exclude_uids = isset($_POST['exclude_uids']) ? $_POST['exclude_uids'] : null;
$exclude_uids = empty($exclude_uids) ? array() : (is_array($exclude_uids) ? $exclude_uids : explode(',', $exclude_uids));
$exclude_client_ids = array();
foreach($exclude_uids as $exclude_uid){
	$client_ids = Gateway::getClientIdByUid($exclude_uid);
	if(is_array($client_ids)) $exclude_client_ids += $client_ids;
}

try{
	Gateway::sendToGroup($token, $data, $exclude_client_ids);
	output_success();
}catch(\Exception $e){
	output_error($e->getMessage());
}
