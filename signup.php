<?php

	define('DB_NAME', "FlowDB");
	define('DB_USER', "root");
	define('DB_PASSWORD', "");
	define('DB_HOST', "localhost");

    $link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
    if (!$link){    //checking if connected to host
        die('Could not connect: ' . mysql_error());
    }

    $db_selected = mysql_select_db(DB_NAME, $link);
    if (!$db_selected){   //checking if connected to the db
        die('Can\'t use ' . DB_NAME . ': ' . mysql_error());
    }

    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $name = $_POST['name'];

    if ($phone == ""){
        $phone = "no phone";
        $notification = 1;
    } else {
        $notification = 0;
    }

    $subject="SUBJECT";
    $message="MESSAGE";
    $headers = 'From: theflowteam@gmail.com' . "\r\n" .
               'Reply-To: theflowteam@gmail.com';
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\n";
            if(mail($email, $subject, $message, $headers)){
                echo "<br> mail sent!";
            }
            else{
                echo '<br> could not send mail';
            }

    require('/twilio-php/Services/Twilio.php');

    $account_sid = 'ACa37405b14996a99320a5f00870aec4fc';
    $auth_token = 'f323472699cb4912ce522a922be92316';
    $client = new Services_Twilio($account_sid, $auth_token);

    $client->account->messages->create(array(
   	'To' => "6474690372",
   	'From' => "+12892733742",
   	'Body' => "Body Text",
    ));

     $sql = "INSERT INTO Donator (email, name, notification, password, phone, username) VALUES ('$email', '$name', '$notification', '$password', '$phone', '$email')";

    // notification: 0 is SMS, 1 is Email, Username is the same as email

    if (!mysql_query($sql)){
        die('Can\'t query: ' . mysql_error());
    }

    echo '<meta http-equiv="refresh" content="0; URL=/">';
?>