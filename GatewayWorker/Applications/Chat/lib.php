<?php

/**
 * 输出json格式信息
 *
 * @param       $code
 * @param       $msg
 * @param       $data
 * @param array $extend
 */
function output($code, $msg, $data = array(), $extend = array()){
	$message = json_encode(array_merge(array(
		'code' => $code,
		'msg' => $msg,
		'data' => $data
	), $extend));
	echo $message;
	exit(0);
}

/**
 * 输出json格式成功信息
 *
 * @param       $data
 * @param array $extend
 */
function output_success($data = array(), $extend = array()){
	output(1, 'ok', $data, $extend);
}

/**
 * 输出json格式错误信息
 *
 * @param string $msg
 * @param int    $code
 * @param array  $extend
 */
function output_error($msg, $code = 1, $extend = array()){
	output($code, $msg, array(), $extend);
}