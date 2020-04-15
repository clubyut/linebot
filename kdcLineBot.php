<?php


$access_token = 'khMTUZHPuXoT1Dl3A6d6To9TUYRCJx1liGaFnsw4wy8XXwSX9lQuN/qIeX1kJZx7JXUQG3GRaALODI8VbNK9/kTnMIED3+38dQ/BZkF2l2pVI5FWo/1VEgDkYq6hhielFp6rk/SkPesJWwz+j2t2hQdB04t89/1O/w1cDnyilFU=';

$content = file_get_contents('php://input');

$events = json_decode($content, true);



define('UPLOAD_DIR', 'tmp_image/');
/*Get Data From POST Http Request*/
$datas = file_get_contents('php://input');
/*Decode Json From LINE Data Body*/
$deCode = json_decode($datas, true);
file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);

$LINEDatas['token'] = $access_token;
$messageType = $deCode['events'][0]['message']['type'];

$servername = "xxxxxxxx";

$username = "xxxxxxxx";

$password = "xxxxxxxx";

$dbx = "cp572795_KDC";

$conn = new mysqli($servername, $username, $password, $dbx);

mysqli_set_charset($conn, "utf8");

//mysqli_set_charset($conn,"TIS620");

mysql_query("SET NAMES utf8");



$link = mysql_connect("xxxxxxx", "xxxxxxx", "xxxxxx");

mysql_select_db("cp572795_KDC", $link);



// $getResult = '';

if (!is_null($events['events'])) {

	$okreturn = 0;

	foreach ($events['events'] as $event) {

		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {

			$return = '';

			$replyToken = $event['replyToken'];

			$userId = $event['source']['userId'];

			$userX = $event['source']['userId'];

			$id = $event['message']['id'];

			$text = $event['message']['text'];

			$numrows = 0;

			$messagesX = array(1);

			$resp = '';



			if ($text == 'xxx') {
				$resp = 'ถ่ายรูป 3 รูปเพื่อจบงาน3';
			}
			if (strtoupper(substr($text, 0, 2)) == 'DE') {
				$jtext = substr($text, 3);
				mysql_query("SET NAMES utf8");
				$result = mysql_query("SELECT  `ID`,  `WorkOrder`,  LEFT(`Detail`, 256) as Detail,  `AssignDate`,  `ReceiveDate`,  LEFT(`ReceiverProfile`, 512) as ReceiverProfile,  LEFT(`ImageID1`, 256),  LEFT(`ImagePath1`, 256) as ImagePath1,  LEFT(`ImageID2`, 256),  LEFT(`ImagePath2`, 256),  LEFT(`GPS`, 256) FROM `cp572795_KDC`.`WorkOrder` WHERE UPPER(`WorkOrder`) = UPPER('" . $jtext . "');");
				$num_rows = mysql_num_rows($result);
				if ($num_rows > 0) {
					$err = mysql_query("DELETE FROM `cp572795_KDC`.`ReceiveActive` WHERE  `LineID`='" . $userX . "';");
					$err = mysql_query("INSERT INTO `cp572795_KDC`.`ReceiveActive` (`LineID`, `WorkOrderActive`) VALUES ('" . $userX . "', '" . $jtext . "');");

					
					$ret2 = "UPDATE `cp572795_KDC`.`WorkOrder` SET `ReceiverProfile` = NULL,`ImageID1`= NULL, `ImagePath1`= NULL WHERE  UPPER(`WorkOrder`)=UPPER('" . $jtext . "');";
					$err_UPD = mysql_query($ret2);

					$messagesX = array(1);
					
					$resp = $row["Detail"] . " และทำการถ่ายรูปกล่องกลับมาด้วย ของงานเลขที่ = " . $jtext;
					$messages = [
						'type' => 'text',
						'text' => $resp, //."   SELECT  * FROM `cp572795_KDC`.`WorkOrder` WHERE `ReceiverProfile` IS NULL and `WorkOrder` LIKE '" . $text . "%';", //. "DELETE FROM `cp572795_KDC`.`ReceiveActive` WHERE  `LineID`='" . $userX . "'; INSERT INTO `cp572795_KDC`.`ReceiveActive` (`LineID`, `WorkOrderActive`) VALUES ('" . $userX . "', '" . $text . "');",
						'quickReply' => [
							'items' => [
								[
									'type' => 'action',
									'action' => [
										'type' => 'camera',
										'label' => 'Camera'
									]
								]
							]
						]
					];

					$messagesX[0] = $messages;
					_sendOut($access_token, $replyToken, $messagesX);

				}
			} else {

				mysql_query("SET NAMES utf8");
				$result = mysql_query("SELECT  `ID`,  `WorkOrder`,  LEFT(`Detail`, 256) as Detail,  `AssignDate`,  `ReceiveDate`,  LEFT(`ReceiverProfile`, 512) as ReceiverProfile,  LEFT(`ImageID1`, 256),  LEFT(`ImagePath1`, 256) as ImagePath1,  LEFT(`ImageID2`, 256),  LEFT(`ImagePath2`, 256),  LEFT(`GPS`, 256) FROM `cp572795_KDC`.`WorkOrder` WHERE UPPER(`WorkOrder`) = UPPER('" . $text . "');");
				$num_rows = mysql_num_rows($result);

				//$resp = 'ถ่ายรูป 3 รูปเพื่อจบงาน8 ==> '.$num_rows." :SELECT  `ID`,  `WorkOrder`,  LEFT(`Detail`, 256) as Detail,  `AssignDate`,  `ReceiveDate`,  LEFT(`ReceiverID`, 256),  LEFT(`ImageID1`, 256),  LEFT(`ImagePath1`, 256),  LEFT(`ImageID2`, 256),  LEFT(`ImagePath2`, 256),  LEFT(`GPS`, 256) FROM `cp572795_KDC`.`WorkOrder` WHERE `WorkOrder` LIKE '" . $text . "%';";
				if ($num_rows > 0) {

					$result_and_receiveฺ = mysql_query("SELECT  * FROM `cp572795_KDC`.`WorkOrder` WHERE `ReceiverProfile` IS NULL and UPPER(`WorkOrder`) = UPPER('" . $text . "');");

					$num_rows_reveive = mysql_num_rows($result_and_receiveฺ);

					if ($num_rows_reveive > 0) {
						$row = mysql_fetch_array($result, 0);
						$resp = $row["Detail"] . " และทำการถ่ายรูปกล่องกลับมาด้วย ของงานเลขที่ = " . $text;
						$err = mysql_query("DELETE FROM `cp572795_KDC`.`ReceiveActive` WHERE  `LineID`='" . $userX . "';");
						$err = mysql_query("INSERT INTO `cp572795_KDC`.`ReceiveActive` (`LineID`, `WorkOrderActive`) VALUES ('" . $userX . "', '" . $text . "');");
						$messages = [
							'type' => 'text',
							'text' => $resp, //."   SELECT  * FROM `cp572795_KDC`.`WorkOrder` WHERE `ReceiverProfile` IS NULL and `WorkOrder` LIKE '" . $text . "%';", //. "DELETE FROM `cp572795_KDC`.`ReceiveActive` WHERE  `LineID`='" . $userX . "'; INSERT INTO `cp572795_KDC`.`ReceiveActive` (`LineID`, `WorkOrderActive`) VALUES ('" . $userX . "', '" . $text . "');",
							'quickReply' => [
								'items' => [
									[
										'type' => 'action',
										'action' => [
											'type' => 'camera',
											'label' => 'Camera'
										]
									]
								]
							]
						];
						$messagesX[0] = $messages;
						_sendOut($access_token, $replyToken, $messagesX);
					} else {

						$row = mysql_fetch_array($result, 0);
						$profile = $row["ReceiverProfile"];
						$img = $row["ImagePath1"];


						$messagesX = array(5);

						$messages = [
							'type' => 'text',
							'text' => 'งานเลขที่นี้รับแล้ว'
						];

						$messagesPicture = [
							'type' => 'image',
							'originalContentUrl' => 'https://linequery.com/' . $img,
							'previewImageUrl' => 'https://linequery.com/' . $img
						];


						$json = substr($profile, 1);
						$json = json_decode($json, true);
						// echo $json['displayName'];
						// echo $json['pictureUrl'];

						$messagesProfileReceiver = [
							'type' => 'text',
							'text' => 'ผู้รับงาน:' . $json['displayName']
						];


						$messagesPictureReceiver = [
							'type' => 'image',
							'originalContentUrl' => $json['pictureUrl'],
							'previewImageUrl' => $json['pictureUrl']
						];

						$messagesDelReq = [
							'type' => 'text',
							'text' => 'หากต้องการรับเอกสารใหม่ให้กดที่ ลบรูปถ่าย ',
							'quickReply' => [
								'items' => [
									[
										'type' => 'action',
										'action' => [
											'type' => 'message',
											'label' => 'ลบรูปถ่าย',
											'text' => 'DE:'.$text
										]
									]
								]
							]
						];

						$messagesX[0] = $messages;
						$messagesX[1] = $messagesPicture;
						$messagesX[2] = $messagesProfileReceiver;
						$messagesX[3] = $messagesPictureReceiver;
						$messagesX[4] = $messagesDelReq;
						_sendOut($access_token, $replyToken, $messagesX);
					}
				} else {
					$messagesX = array(1);

					$messages = [
						'type' => 'text',
						'text' => 'ไม่พบเวิร์คงานหรือคำสั่งที่ระบุ'
					];
					$messagesX[0] = $messages;

					_sendOut($access_token, $replyToken, $messagesX);
				}
			}
		} elseif ($event['type'] == 'follow' && $event['source']['type'] == 'user') {

			$userId = $event['source']['userId'];

			$replyToken = $event['replyToken'];

			$messagesX = array(1);



			$messages = [

				'type' => 'text',

				'text' => 'ขอต้อนรับเข้าสู่ระบบใหม่กรุณาพิมพ์ ? เพื่่อดูการสั่งค่า'

			];

			$messagesX[0] = $messages;



			// $cmd = "INSERT INTO `cp572795_start`.`XCL_SHOP_PERSON` (`LINE_TOKEN`) VALUES ('" . $userId . "');";

			// $result = $conn->query($cmd);



			_sendOut($access_token, $replyToken, $messagesX);
		} elseif ($messageType == 'image') {

			$replyToken = $event['replyToken'];
			$userId = $event['source']['userId'];
			$userX = $event['source']['userId'];
			$id = $event['message']['id'];
			$text = $event['message']['text'];

			$LINEDatas['messageId'] = $deCode['events'][0]['message']['id'];
			$results = getContent($LINEDatas);
			$uid = uniqid();
			if ($results['result'] == 'S') {
				$file = UPLOAD_DIR . $uid . '.png';
				$success = file_put_contents($file, $results['response']);
			}



			$LINEDatasP['url'] = "https://api.line.me/v2/bot/profile/" . $userId;
			$LINEDatasP['token'] = $access_token;
			$profile = getLINEProfile($LINEDatasP);

			mysql_query("SET NAMES utf8");
			$result = mysql_query("SELECT * FROM `cp572795_KDC`.`ReceiveActive` WHERE `LineID` = '" . $userX . "'");
			$num_rows = mysql_num_rows($result);

			$ret2 = '';



			if ($num_rows > 0) {

				$row = mysql_fetch_array($result, 0);

				$wo = $row["WorkOrderActive"];

				$profileText = implode("", $profile);

				$ret2 = "UPDATE `cp572795_KDC`.`WorkOrder` SET `ReceiverProfile` = '" . $profileText . "',`ImageID1`='" . $uid . "', `ImagePath1`='" . $file . "' WHERE  UPPER(`WorkOrder`)=UPPER('" . $wo . "');";

				$err_UPD = mysql_query($ret2);

				$messagesX = array(1);

				$messages = [

					'type' => 'text',

					'text' => 'งานเลขที่ : ' . $wo . 'บันทึกเรียบร้อย'

				];

				$messagesX[0] = $messages;

				_sendOut($access_token, $replyToken, $messagesX);
			}
		} else {

			$replyToken = $event['replyToken'];

			$messagesX = array(1);



			$messages = [

				'type' => 'text',

				'text' => json_encode($content)

			];

			$messagesX[0] = $messages;



			_sendOut($access_token, $replyToken, $messagesX);
		}
	}
}

function getContent($datas)
{
	$datasReturn = [];
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://api.line.me/v2/bot/message/" . $datas['messageId'] . "/content",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_POSTFIELDS => "",
		CURLOPT_HTTPHEADER => array(
			"Authorization: Bearer " . $datas['token'],
			"cache-control: no-cache"
		),
	));
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);

	if ($err) {
		$datasReturn['result'] = 'E';
		$datasReturn['message'] = $err;
	} else {
		$datasReturn['result'] = 'S';
		$datasReturn['message'] = 'Success';
		$datasReturn['response'] = $response;
	}

	return $datasReturn;
}


function getLINEProfile($datas)
{
	$datasReturn = [];
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => $datas['url'],
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
			"Authorization: Bearer " . $datas['token'],
			"cache-control: no-cache"
		),
	));
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	if ($err) {
		$datasReturn['result'] = 'E';
		$datasReturn['message'] = $err;
	} else {
		if ($response == "{}") {
			$datasReturn['result'] = 'S';
			$datasReturn['message'] = 'Success';
		} else {
			$datasReturn['result'] = 'E';
			$datasReturn['message'] = $response;
		}
	}
	return $datasReturn;
}

function _sendOut($access_token, $replyToken, $messagesX)
{

	$url = 'https://api.line.me/v2/bot/message/reply';

	$data = [

		'replyToken' => $replyToken,

		'messages' => $messagesX,

	];



	$post = json_encode($data);

	$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);




	// $deCode = json_decode($post, true);
	// file_put_contents('log2.txt', implode("", $data) , FILE_APPEND);


	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	$result = curl_exec($ch);

	curl_close($ch);



	echo $result . "\r\n";
}



echo "OK";
