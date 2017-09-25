<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Services\PushNotifications;

class HomeController extends Controller
{
    public function index()
    {
        return 'Hello';
    }

    public function sendNotification() {
        $response = $this->sendMessage();
        $return["allresponses"] = $response;
        $return = json_encode( $return);

        return "Hello form send notification";
    }

    private function sendMessage(){
		$content = array(
			"en" => 'English Message'
			);
		
		$fields = array(
			'app_id' => "a03d3efb-1020-4993-9651-72a10b06b7d4",
			'included_segments' => array('All'),
            'data' => array("foo" => "bar"),
			'contents' => $content
		);
		
		$fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic ZDY3ODA1NTAtMDlkMS00YzRhLTgzMzMtMGI3ZTE4NWY1MTBh'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}
}
