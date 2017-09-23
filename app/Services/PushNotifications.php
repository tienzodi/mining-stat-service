<?php namespace App\Services;

use App\Notification;
use App\User;
use Log;

class PushNotifications {

	public static function sendNotificationToMobile($notification)
    {
    	$user = User::findOrFail($notification['user_id']);

    	if($user)
    	{
    		if(strlen($user->ios_token) != 0)
    		{
    			PushNotifications::sendMessageToIOS($notification['content'], $user->ios_token);
    		}

    		if(strlen($user->android_token) != 0)
    		{
    			PushNotifications::sendMessageToAndroid($notification['content'], $user->android_token);
    		}
	    }
    }

    private static function sendMessageToAndroid($message, $registration_ids)
    {
    	$url = 'https://android.googleapis.com/gcm/send';
	    $fields = array(
	        'registration_ids' => $registration_ids,
	        'data' => $message,
	    );

	    $GOOGLE_API_KEY = 'AIzaSyAksvJBlp40X38uuoMDcTSmtx7_ZjhNQug';

	    $headers = array(
	        'Authorization:key=' . $GOOGLE_API_KEY,
	        'Content-Type: application/json'
	    );
	    echo json_encode($fields);
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

	    $result = curl_exec($ch);
	    if($result === false)
	        die('Curl failed ' . curl_error());

	    curl_close($ch);
	    return $result;
    }

    private static function sendMessageToIOS($message, $deviceToken)
    {
		Log::info($message);
		
		// Put your private key's passphrase here:
		$passphrase = '123456';

		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', app_path().'/Services/ck.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

		// Open a connection to the APNS server
		$fp = stream_socket_client(
			'ssl://gateway.sandbox.push.apple.com:2195', $err,
			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

		if (!$fp)
			Log::error("Failed to connect: $err $errstr");

		Log::info('Connected to APNS' . PHP_EOL);

		// Create the payload body
		$body['aps'] = array(
			'alert' => $message,
			'sound' => 'default',
			'content-available' => 1
			);

		// Encode the payload as JSON
		$payload = json_encode($body);

		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));

		if (!$result)
			Log::info('Message not delivered');
		else
			Log::info('Message successfully delivered');

		// Close the connection to the server
		fclose($fp);
    }
}