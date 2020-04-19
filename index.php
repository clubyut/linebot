<?php


$API_URL = 'https://api.line.me/v2/bot/message';
$ACCESS_TOKEN = 'yK9Mley/uEEGeEeVjkR2UHggFuwqO1yeg149LN0lUSG5/NgXxcgwYgzm3A5FOp+SfPbpCESrotui1CLv2YEdcsirvcKET+u8EaPNPHhVWdIGJgUewZYFbq6lOZzhftK6akBtUm2rkFOyUVdL1B/URwdB04t89/1O/w1cDnyilFU='; 
$channelSecret = 'f9629f9dedd8637ddd1ff39c02ca9ae1';


$POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);

$request = file_get_contents('php://input');   // Get request content
$request_array = json_decode($request, true);   // Decode JSON to Array


$access_token = 'yK9Mley/uEEGeEeVjkR2UHggFuwqO1yeg149LN0lUSG5/NgXxcgwYgzm3A5FOp+SfPbpCESrotui1CLv2YEdcsirvcKET+u8EaPNPHhVWdIGJgUewZYFbq6lOZzhftK6akBtUm2rkFOyUVdL1B/URwdB04t89/1O/w1cDnyilFU=';

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

if (!is_null($events['events'])) {
	$okreturn = 0;

	foreach ($events['events'] as $event) {
			$return = '';

			$replyToken = $event['replyToken'];

			$userId = $event['source']['userId'];

			$userX = $event['source']['userId'];

			$id = $event['message']['id'];

			$text = $event['message']['text'];

			$numrows = 0;

			//$messagesX = array();

			$resp = '';
		



if ($event['message']['type'] == 'image')  {

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
				file_put_contents($file, $data);
				//$success = file_put_contents($file, $results['response']);
			}


			$LINEDatasP['url'] = "https://api.line.me/v2/bot/profile/" . $userId;
			$LINEDatasP['token'] = $access_token;
			$profile = getLINEProfile($LINEDatasP);
			$profileText = implode("", $profile);
				$messagesX = array(0);
				$messages = [

				'type' => 'text',

				'text' => 'ชนิดข้อมูลที่ส่ง:'.$file.$profileText

			];


			$response = $bot->getMessageContent($event['message']['id']);
			if ($response->isSucceeded()) {
					$dataBinary = $response->getRawBody();
						//chdir('admins');
					$fileFullSavePath = 'ชื่อรูปภาพ.jpg';
					file_put_contents($fileFullSavePath,$dataBinary);
				}
			
			$messagesX[0] = $messages;
			_sendOut($access_token, $replyToken, $messagesX);

			}else
			{
				$messagesX = array(0);
				$messages1 = [

				'type' => 'text',

				'text' => 'ชนิดข้อมูลที่ส่ง:'.$event['message']['type']

			];
			$resp = " กรุณาถ่ายรูปเพื่อแนบรูปถ่ายค่ะ";
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
			
	}

}

echo "OK";


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

function send_reply_message($url, $post_header, $post_body)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

?>