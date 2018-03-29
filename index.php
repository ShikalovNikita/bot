<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require 'vendor/autoload.php';

use GuzzleHttp\Client;

class TelegramBot {
	protected $token = "582545126:AAGJjd_VMvKER_BydgO1JW-jBwD9sWotLdQ";
	protected $updateID;

	protected function query($method, $params = array()) {
		$url = "https://api.telegram.org/bot";
		$url .= $this->token;
		$url .= "/".$method;
		if(!empty($params)) {
			$url .= "?".http_build_query($params);
		}
		$client = new Client([
			'base_uri' => $url
		]);

		$result = $client->request('GET');

		return json_decode($result->getBody());
	}

	public function getUpdates() {
		$response = $this->query('getUpdates', [
			'offset' => $this->updateID + 1,
		]);

		if( !empty($response->result) )
			$this->updateID = $response->result[count($response->result) - 1]->update_id;
		return $response->result;
	}
	public function sendMessage($chat_id, $text) {
		$response = $this->query('sendMessage', [
			'text' => $text,
			'chat_id' => $chat_id
		]);
		return $response;
	}
}

$bot = new TelegramBot();
while (true) {
	
	sleep(2);

	$updates = $bot->getUpdates();

	foreach ($updates as $update) {
		$bot->sendMessage($update->message->chat->id, $update->message->text);
	}
}