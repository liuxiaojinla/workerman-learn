<?php
use GatewayWorker\Lib\Gateway;

require_once __DIR__.'/../../vendor/autoload.php';
require_once './lib.php';

$uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
if($uid < 1) output_error('uid invalid！');

$data = isset($_POST['data']) ? $_POST['data'] : '';
if(empty($data)) output_error('data invalid！');

try{
	if(Gateway::isUidOnline($uid)){
		Gateway::sendToUid($uid, $data);
	}
	output_success();
}catch(\Exception $e){
	output_error($e->getMessage());
}
