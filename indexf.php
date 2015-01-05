<?php
function indexf($SID, $amount) {
	require "aaa.php";
	define('DB_NAME', "FlowDB");
	define('DB_USER', "flowit");
	define('DB_PASSWORD', "Spring300");
	define('DB_HOST', "localhost");

	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if (!$link){    //checking if connected to host
		die('Could not connect: ' . mysql_error());
	}

	$db_selected = mysql_select_db(DB_NAME, $link);
	if (!$db_selected){   //checking if connected to the db
		die('Can\'t use ' . DB_NAME . ': ' . mysql_error());
	}

	//$amount = 35;  //simulating, receive in index.php 

	$sql = "SHOW TABLES FROM FlowDB";
	$result = mysql_query($sql);
	if (!$result) {
		echo 'no more Donations available';  //empty table (Donation)
	}
	else {
		
		$sql = "SELECT amount FROM Donation";
		$result = mysql_query($sql);
		$totalsum = 0;
	   while($row = mysql_fetch_array($result)){
		   $totalsum += $row['amount'];
	   }
	   if ($totalsum < $amount) {  //die if total amount cannot pay for the item
			die("cannot donate");
	   }
		$Lastdate = mysql_fetch_array(mysql_query("SELECT * FROM Donation ORDER BY date_time"))['date_time'];
		$Lastid = mysql_fetch_array(mysql_query("SELECT * FROM Donation ORDER BY date_time"))['username'];
		$phone = mysql_fetch_array(mysql_query("SELECT * FROM Donator WHERE username='$Lastid'"))['phone'];  //lastid's phone
		$LastAmount = mysql_fetch_array(mysql_query("SELECT * FROM Donation ORDER BY date_time"))['amount'];  //lastid's Donation

		$fullname = mysql_fetch_array(mysql_query("SELECT * FROM Donator WHERE username='$Lastid'"))['name'];

		$storename = mysql_fetch_array(mysql_query("SELECT * FROM Store WHERE SID = '$SID'"))['name'];

		$storeproduct = mysql_fetch_array(mysql_query("SELECT * FROM Store WHERE SID = '$SID'"))['product'];
		
			if ($LastAmount >= $amount) {
				$diff = $LastAmount - $amount;
				if ($diff != 0) {$sql = "UPDATE Donation SET amount='$diff' WHERE username='$Lastid'";}
				else {$sql = "DELETE FROM Donation WHERE username='$Lastid'";}			
				if (!mysql_query($sql)) {
					die(mysql_error());
				}
				send_text($phone, $Lastid, $fullname, $storename, $storeproduct, $amount);

			}
			
			else {
				while ($amount > 0) {
					$flag = ($LastAmount <= $amount);
					$diff = $LastAmount - $amount;
					$amount -= $LastAmount;
					if ($flag) {
						$sql = "DELETE FROM Donation WHERE username='$Lastid'"; 
						send_text($phone, $Lastid, $fullname, $storename, $storeproduct, $LastAmount);
					}
					else {
						$sql = "UPDATE Donation SET amount='$diff' WHERE username='$Lastid'";
						send_text($phone, $Lastid, $fullname, $storename, $storeproduct, $amount);
					}
					if (!mysql_query($sql)) {
						die(mysql_error());
					}
					

					$Lastdate = mysql_fetch_array(mysql_query("SELECT * FROM Donation ORDER BY date_time"))['date_time'];
					$Lastid = mysql_fetch_array(mysql_query("SELECT * FROM Donation ORDER BY date_time"))['username'];
					$phone = mysql_fetch_array(mysql_query("SELECT * FROM Donator WHERE username='$Lastid'"))['phone'];  
					$LastAmount = mysql_fetch_array(mysql_query("SELECT * FROM Donation ORDER BY date_time"))['amount'];  				
				}
			}
		
	}
}
?>