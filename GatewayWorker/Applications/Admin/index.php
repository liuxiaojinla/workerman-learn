<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>消息推送测试</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<style type="text/css">
		body {
			padding: 0;
			margin: 0;
		}

		* {
			box-sizing: border-box;
		}

		label {
			padding: 5px 10px;
			line-height: 21px;
			display: inline-block;
		}

		input, textarea, select {
			display: block;
			padding: 8px 15px;
			line-height: 21px;
			border: 1px solid #dddddd;
			border-radius: 0;
			width: 100%;
		}

		input:focus,
		textarea:focus,
		select:focus {
			outline: 0;
			border-color: darkcyan;
		}

		button {
			display: inline-block;
			padding: 10px 15px;
			min-width: 80px;
			border: 0;
			color: white;
			background-color: #f9f9f9;
		}

		button:active {
			background-color: #e0e0e0;
		}

		button:focus {
			outline: 0;
		}

		button[type="submit"] {
			box-shadow: 1px 2px 3px #004444;
			background-color: darkcyan;
		}

		button[type="submit"]:active {
			box-shadow: 1px 2px 3px #003333;
			background-color: #006666;
		}

		.form {
			background-color: white;
			position: relative;
			margin-top: 10px;
		}

		.form-row {
			display: table;
			table-layout: fixed;
			width: 100%;
			margin-top: 10px;
		}

		.form-row > label,
		.form-row > .form-control {
			display: table-cell;
			vertical-align: top;
		}

		.form-row > label {
			width: 150px;

			text-align: right;
		}

		.form-button {
			padding: 10px 10px 10px 150px;
		}

		.alert {
			padding: 10px;
			border-radius: 5px;
			margin-top: 10px;
			margin-left: 150px;
		}

		.alert > h1 {
			font-size: 24px;
			margin-top: 0.2em;
			margin-bottom: 0.2em;
		}

		.alert-default {
			background-color: #e0e0e0;
			color: #333;
		}

		.alert-success {
			background-color: darkcyan;
			color: white;
			box-shadow: 2px 2px 2px #006666;
		}

		.alert-error {
			background-color: indianred;
			color: white;
			box-shadow: 2px 2px 2px #C55050
		}

		.container {
			max-width: 640px;
			margin: 45px auto;
		}
	</style>
</head>
<body>
<div class="container">
	<?php
	//	ini_set('display_errors', 'on');
	use GatewayWorker\Lib\Gateway;

	require_once __DIR__.'/../../vendor/autoload.php';

	define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
	define('IS_GET', REQUEST_METHOD == "GET");
	define('IS_POST', REQUEST_METHOD == "POST");

	//创建一个消息Id
	function createMsgId(){
		list($s1, $s2) = explode(' ', microtime());
		return 'm'.((float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000));
	}

	if(IS_POST){
		$clientId = $_POST['client_id'];
		$type = $_POST['type'];
		$content = $_POST['content'];
		$data = json_encode([
			'id' => createMsgId(),
			'type' => $type,
			'data' => [
				'type' => 'text',
				'text' => $content
			]
		]);
		try{
			if(empty($content)) throw new Exception('内容不得为空！');

			if($type == 'push'){
				Gateway::sendToAll($data);
			}else{
				Gateway::sendToClient($clientId, $data);
			}
			echo '<div id="tips" class="alert alert-success"><h1 class="success">发送成功</h1></div>';
		}catch(\Exception $e){
			echo '<div id="tips" class="alert alert-error"><h1 class="error">'.$e->getMessage().'</h1></div>';
		}
		echo '<div class="alert alert-default">'.$data.'</div>';
	}
	?>
	<form action="index.php" method="post" class="form">
		<div class="form-row">
			<label for="client_id">client id</label>
			<div class="form-control">
				<input type="text" id="client_id" name="client_id" value="<?php echo $_POST['client_id'] ?>">
			</div>
		</div>
		<div class="form-row">
			<label for="client_id">消息类型</label>
			<div class="form-control">
				<select name="type">
					<option value="say" <?php echo $_POST['type'] == 'say' ? 'selected' : '' ?>>普通消息</option>
					<option value="push" <?php echo $_POST['type'] == 'push' ? 'selected' : '' ?>>消息推送</option>
				</select>
			</div>
		</div>
		<div class="form-row">
			<label for="content">内容</label>
			<div class="form-control">
				<textarea id="content" name="content" rows="4"></textarea>
			</div>
		</div>
		<div class="form-button">
			<button type="submit">发送</button>
		</div>
	</form>
</div>
</body>
<script type="text/javascript">
	setTimeout(function () {
		var el = document.querySelector('#tips');
//		el.parentNode.removeChild(el);
	}, 1500);
</script>
</html>
