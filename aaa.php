<?php
function send_text ($phone, $user, $fullname, $storename, $storeproduct, $money) {
    // Step 1: Download the Twilio-PHP library from twilio.com/docs/libraries, 
    // and move it into the folder containing this file.
    require "twilio-php/Services/Twilio.php";
 
    // Step 2: set our AccountSid and AuthToken from www.twilio.com/user/account
    $AccountSid = "ACa37405b14996a99320a5f00870aec4fc";
    $AuthToken = "f323472699cb4912ce522a922be92316";
 
    // Step 3: instantiate a new Twilio Rest Client
    $client = new Services_Twilio($AccountSid, $AuthToken);
 
    // Step 4: make an array of people we know, to send them a message. 
    // Feel free to change/add your own phone number and name here.
	$temp = "+1" . $phone;
    $people = array(
        $temp => "$user",
    );
 
    // Step 5: Loop over all our friends. $number is a phone number above, and 
    // $name is the name next to it
	if ($storeproduct == 1) { $temp = "food";}
	else {
		if ($storeproduct == 2) {
			$temp = "clothes";
		}
	}
    foreach ($people as $number => $name) {

        $sms = $client->account->messages->sendMessage(
 
        // Step 6: Change the 'From' number below to be a valid Twilio number 
        // that you've purchased, or the (deprecated) Sandbox number
            "+12892733742", 
 
            // the number we are sending to - Any phone number
            $number,

            // the sms body
            "Hello $fullname! Today your donation contributed $money dollars to  help someone buy $temp from $storename! Keep with the Flow! :D"
        );
 
        // Display a confirmation message on the screen
        //echo "Sent message to $name";
    }
}
?>