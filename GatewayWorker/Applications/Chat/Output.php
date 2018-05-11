<?php
use GatewayWorker\Lib\Gateway;

/**
 * 发送到客户端
 */
trait Output{

	/**
	 * 给当前用户发生信息
	 *
	 * @param string $msgId
	 * @param int    $code
	 * @param string $msg
	 * @param array  $data
	 * @param array  $extend
	 * @return int
	 */
	public static function send($msgId, $code, $msg, $data = array(), $extend = array()){
		$message = json_encode(array_merge(array(
			'id' => $msgId,
			'code' => $code,
			'msg' => $msg,
			'data' => $data
		), $extend));
		$length = strlen($message);

		Gateway::sendToCurrentClient($message);
		return $length;
	}

	/**
	 * 发送成功信息
	 *
	 * @param string $msgId
	 * @param array  $data
	 * @param array  $extend
	 * @return int
	 */
	public static function sendSuccess($msgId, $data = array(), $extend = array()){
		return self::send($msgId, 1, 'ok', $data, $extend);
	}

	/**
	 * 发送错误信息
	 *
	 * @param string $msgId
	 * @param string $msg
	 * @param int    $code
	 * @param array  $extend
	 * @return int
	 */
	public static function sendError($msgId, $msg, $code = 1, $extend = array()){
		return self::send($msgId, $code, $msg, array(), $extend);
	}

}