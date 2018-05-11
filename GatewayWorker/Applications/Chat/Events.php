<?php
use GatewayWorker\Lib\Gateway;
use Workerman\MySQL\Connection;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events{

	use Output;

	/**
	 * 数据库连接实例
	 *
	 * @var Connection
	 */
	protected static $db;

	/**
	 * 业务进程已打开
	 *
	 * @param $businessWorker
	 */
	public static function onWorkerStart($businessWorker){
		$host = 'rdsyy6j2uzqezyuo.mysql.rds.aliyuncs.com';
		$port = '3306';
		$user = 'duoguantest2';
		$pwd = 'Dg2017892*%';
		$dbName = 'weiphp';
		self::$db = new Connection($host, $port, $user, $pwd, $dbName);
	}

	/**
	 * 业务进程已关闭
	 *
	 * @param $businessWorker
	 */
	public static function onWorkerStop($businessWorker){
		if(self::$db){
			self::$db->closeConnection();
		}
	}

	/**
	 * 当客户端连接时触发
	 * 如果业务不需此回调可以删除onConnect
	 *
	 * @param int $clientId 连接id
	 */
	public static function onConnect($clientId){
//		$data = array(
//			'id' => self::createMsgId(),
//			'type' => 'user_info',
//			'data' => array(
//				'client_id' => $clientId,
//			),
//		);
//		// 向当前client_id发送数据
//		Gateway::sendToClient($clientId, json_encode($data));
	}

	/**
	 * 创建一个消息ID
	 *
	 * @return string
	 */
	public static function createMsgId(){
		list($s1, $s2) = explode(' ', microtime());
		return 'm'.((float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000));
	}

	/**
	 * 当客户端发来消息时触发
	 *
	 * @param int   $clientId 连接id
	 * @param mixed $message 具体消息
	 * @throws Exception
	 */
	public static function onMessage($clientId, $message){
		$data = json_decode($message, true);
		$type = $data['type'];
		if('say' == $type) self::onSayMessage($data, $message);
		elseif('login' == $type) self::onLogin($clientId, $data);
		elseif('logout' == $type) Gateway::closeCurrentClient();
		// // 向所有人发送
		// Gateway::sendToAll("$client_id said $message");
	}

	/**
	 * 发送信息
	 *
	 * @param array  $data
	 * @param string $message
	 */
	public static function onSayMessage($data, $message){
		if(empty($data['form'])) return;
		if(empty($data['to'])) return;
		Gateway::sendToClient($data['to'], $message);
	}

	/**
	 * 登录服务器
	 *
	 * @param string $clientId
	 * @param array  $data
	 * @return mixed
	 */
	public static function onLogin($clientId, $data){
		$msgId = $data['id'];
		$data = $data['data'];
		$uid = isset($data['uid']) ? intval($data['uid']) : 0;
		if($uid < 1) return self::sendError($msgId, 'uid invalid！');

		//根据uid查询用户信息
		$field = 'uid,nickname,mobile,sex,headimgurl,agent_id';
		$userInfo = self::$db->select($field)->from('wp_user')->where('uid= :uid')->bindValues(array(
			'uid' => $uid
		))->row();
		if(empty($userInfo)) return self::sendError($msgId, 'user not found！');

		//查询组id
		$token = self::$db->select('token')->from('wp_public_follow')->where('uid= :uid')->bindValues(array(
			'uid' => $uid
		))->single();
		$userInfo['token'] = $token;

		$_SESSION['uid'] = $uid;
		Gateway::bindUid($clientId, $uid);
		Gateway::joinGroup($clientId, $token);

		return self::sendSuccess($msgId, $userInfo);
	}

	/**
	 * 当用户断开连接时触发
	 *
	 * @param int $clientId 连接id
	 * @throws Exception
	 */
	public static function onClose($clientId){
		//		$uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : 0;
		//		if($uid) GateWay::unbindUid($clientId, $uid);
	}
}
