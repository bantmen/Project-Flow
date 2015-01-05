<?php

require "indexf.php";
//require "sample/payment/index.php";
$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";  //link entered on browser
//print_r(parse_url($actual_link));

//if (strpos(parse_url($actual_link)["query"], "Number") !== false) {
//    $newString = substr(parse_url($actual_link)["query"], 7, strlen($actual_link) );
//    echo $newString;
//} ;

if (isset(parse_url($actual_link)["query"])) {
    $query = parse_url($actual_link)["query"];
    $len = strlen($query);
    $query_type = '';
    $cut_point = 0;
    for ($i=0; $i<$len; $i++) {
        $str = substr($query, $i,1);
        if ($str == "%") {
            $cut_point = $i;
            break;
        }
        $query_type .= $str;
    }
    $cut_point += 1;  //because works

    define('DB_NAME', 'FlowDB');                //for the actual host
    define('DB_USER', 'flowit');
    define('DB_PASSWORD', 'Spring300');
    define('DB_HOST', 'localhost');
//    define('DB_NAME', 'FlowDB');                 //for the local host
//    define('DB_USER', 'root');
//    define('DB_PASSWORD', '');
//    define('DB_HOST', 'localhost');

    $link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
    if (!$link){    //checking if connected to host
        die('Could not connect: ' . mysql_error());
    }

    $db_selected = mysql_select_db(DB_NAME, $link);
    if (!$db_selected){   //checking if connected to the db
        die('Can\'t use ' . DB_NAME . ': ' . mysql_error());
    }

    if ($query_type == "Transaction") {
        $tran_array = array(
            "tran_id"  => "",
            "SID" => "",
            "MID" => "",
            "amount" => "",
            "date_time" => "",
        );
        $from_array = "";
        $to_array = "";
        $switch = false;  // false -> retrieving from_array info, true -> retrieving to_array info

        $query_edit = substr(parse_url($actual_link)["query"], $cut_point, $len);
        $len_2 = strlen($query_edit);
        for ($i = 0; $i<$len_2; $i++)  {
            $str = substr($query_edit, $i,1);
            if ($switch == false) {
                if ($str == "=") {
                    $switch = true;
                }
                else {$from_array .= $str;}
            }
            else {
                if ($str == "%") {
                    $tran_array[$from_array] = $to_array;
                    $to_array = "";
                    $from_array = "";
                    $switch = false;
                }
                else {
                    if ($str == "+") {
                        $to_array .= " ";
                    }
                    else {$to_array .= $str;}
                }
            }
        }

        $tran_id = $tran_array["tran_id"];
        $SID = $tran_array["SID"];
        $MID = $tran_array["MID"];
        $amount = $tran_array["amount"];
        $date_time = $tran_array["date_time"];
        // 0 means transaction cannot occur, 1 is the opposite

        $sql = "INSERT INTO Transaction (tran_id, SID, MID, amount, date_time) VALUES ('$tran_id', '$SID', '$MID', '$amount', '$date_time')" ;
        if (!mysql_query($sql)){
            die('Can\'t query: ' . mysql_error());
        }
        //transaction information is added to the database, now check for the SID and product

        $sql = "SELECT product FROM Store WHERE SID='$SID'";
        if (!mysql_query($sql)){
            echo 0, " or query failed, ask tim about it";
            die('Can\'t query: ' . mysql_error());  //either store is not registered or query error
        }
        $product = mysql_fetch_array(mysql_query($sql))['product'];    // food -> 1, clothes -> 2
        if (!$product){
            die("Problem with the above query and/or fetching-1");
        }

        if ($product == 1) {    //food
            $sql = "SELECT food_left FROM Member WHERE MID='$MID'";
            $food_left = mysql_fetch_array(mysql_query($sql))['food_left'];
            if (!$food_left){
                die("Problem with the above query and/or fetching-2");
            }

            if ($food_left - $amount < 0) {
                echo 0;
                die();
            }

            $food_left -= $amount;
            $sql = "UPDATE Member SET food_left='$food_left' WHERE MID='$MID'";
            echo 1;  //CALL MARCUS'S FUNCTION
			indexf($SID, $amount);
			//trans($amount);
            die();

            //CALL TRANSACTION FUNCTION
        }

        elseif ($product == 2) {   //clothes
            $sql = "SELECT clothes_left FROM Member WHERE MID='$MID'";
            $clothes_left = mysql_fetch_array(mysql_query($sql))['clothes_left'];
            if (!$clothes_left){
                die("Problem with the above query and/or fetching-3");
            }

            if ($clothes_left - $amount < 0) {
                echo 0;
                die();
            }

            $clothes_left -= $amount;
            $sql = "UPDATE Member SET clothes_left='$clothes_left' WHERE MID='$MID'";
            echo 1;  //CALL MARCUS'S FUNCTION
			indexf($SID, $amount);
			//trans($amount);
            die();
            //CALL TRANSACTION FUNCTION
        }

        else {    //if not food or clothes, then no transaction
            echo 0;
            die();
        }
    }

    elseif ($query_type == "Donation") {
        $donation_array = array (
            "username"  => "",
            "date_time" => "",
            "amount" => "",
        );
        $from_array = "";
        $to_array = "";
        $switch = false;

        $query_edit = substr(parse_url($actual_link)["query"], $cut_point, $len);
        $len_2 = strlen($query_edit);
        for ($i = 0; $i<$len_2; $i++)  {
            $str = substr($query_edit, $i,1);
            if ($switch == false) {
                if ($str == "=") {
                    $switch = true;
                }
                else {$from_array .= $str;}
            }
            else {
                if ($str == "%") {
                    $donation_array[$from_array] = $to_array;
                    $to_array = "";
                    $from_array = "";
                    $switch = false;
                }
                else {
                    if ($str == "+") {
                        $to_array .= " ";
                    }
                    else {$to_array .= $str;}
                }
            }
        }
        $username = $donation_array["username"];
        $date_time = $donation_array["date_time"];
        $amount = $donation_array["amount"];
        $sql = "INSERT INTO Donation (username, date_time, amount) VALUES ('$username', '$date_time', '$amount')" ;
        if (!mysql_query($sql)){
            die('Can\'t query: ' . mysql_error());
        }
        //transaction information is added to the database, now check for the SID and product

    }

    elseif ($query_type == "Verification") {
        $ver_array = array (
            "username"  => "",
            "password" => "",
        );
        $from_array = "";
        $to_array = "";
        $switch = false;

        $query_edit = substr(parse_url($actual_link)["query"], $cut_point, $len);
        $len_2 = strlen($query_edit);
        for ($i = 0; $i<$len_2; $i++)  {
            $str = substr($query_edit, $i,1);
            if ($switch == false) {
                if ($str == "=") {
                    $switch = true;
                }
                else {$from_array .= $str;}
            }
            else {
                if ($str == "%") {
                    $ver_array[$from_array] = $to_array;
                    $to_array = "";
                    $from_array = "";
                    $switch = false;
                }
                else {
                    if ($str == "+") {
                        $to_array .= " ";
                    }
                    else {$to_array .= $str;}
                }
            }
        }

        $username = $ver_array['username'];
        $password = $ver_array['password'];
        $sql = "SELECT username, password FROM Donator WHERE username='$username' AND password='$password'";
        if (!mysql_query($sql)){
            die('Can\'t query: ' . mysql_error());
        }
        $try = mysql_fetch_array(mysql_query($sql));
        if (!$try) {
           echo 0;
        }
        else {
            echo 1;
        }
    }


}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Flow</title>

    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900' rel='stylesheet' type='text/css'>

</head>
<body>
    <div id="signup">
        <div id="signuptext">Sign Up:</div>
        <form name='emailForm' action="signup.php" method="post" onsubmit="return test()">
            <div id="inputcontainer">
                <div id="left">
                    <input type="text" name="name" maxlength="35" placeholder="Full Name" autofocus>
                    <input type="email" name="email" maxlength="35" placeholder="Email Address" autofocus>
                    <input type="password" name="password" maxlength="35" placeholder="Password" autofocus>
                    <div style="width:240px; margin: 0 auto;"><div style="height: 35px; font-family: 'Lato', sans-serif; font-size: 20px; float: left; margin: 0 20px 0 0; display: table; vertical-align: middle; line-height: 160%;">Notifications:</div>
                    <select name="notification" id="options">
                        <option value="1">Email</option>
                        <option value="0y">SMS</option>
                    </select>
                    </div>
                    <input id="phone" type="text" name="phone" maxlength="35" placeholder="Phone: (xxx) xxx-xxxx" autofocus disabled>

                </div>
            </div>
            <input type="submit" value="Submit">
        <form>
    </div>
    <div id="container">
    <div id="main">
        <div id="textcontainer">
            <div id="header">FLOW.</div>
            <div id="headertext">The Transparent Donation Platform</div>
        </div>

        <div id="buttoncontainer">
            <div class="button button1">Sign Up</div>
            <div class="button button2">Donate</div>
            <div class="button button3">Shops</div>
        </div>

        <!--<div id="alerts"></div>-->

    </div>

    <div class="about panel">
        <div class="pheader">
            The Problem
        </div>
        <div class="pcontent">
            Currently, charities providing resources to those in need suffer from inefficiency. It costs valuable time and money to receive, process and deliver goods. This issue is compounded by lack of positive feedback. Donors dont have a clear idea of where money going and as such they lack incentive to give, not knowing whether their money will reach those in need or will be lost in administration. The solution....?  </div>
    </div>

    <div class="explanation panel">
        <div class="pheader">
            Go with the Flow
        </div>
        <div class="pcontent">
            Flow is a transparent donation platform which combines efficient distribution of money with user feedback to impact local communities. It aims to use technology to create a decentralized system, contributing to the efficient distribution of essential goods and increasing impact per donation. People in need would be eligible to receive a card, distributed by the government which would allow them to buy essential goods from certain stores around the city. Users, donating through the flow platform would have their money added to a pool, accessible by those with a card. Individuals with the card would then be able to go to a participating store and buy the goods of their choice. Upon use of their money in a transaction, donors would would receive a notification, informing them on how their donations have been put to use.
        </div>
    </div>

    <div class="stores panel">
        <div class="pheader">
            Stores
        </div>
        <div class="pcontent">
            The following list of participating stores accept flow card payments:
        </div>
        <table id="storelist">
            <tr>
                <td class="one underline">Store:</td>
                <td class="two underline">Location:</td>
                <td class="underline">Contact:</td>
            </tr>
            <tr>
                <td class="one">Subway</td>
                <td class="two">170 Bloor W<br/>Toronto, ON. M5S 1T9</td>
                <td >Mon - Sat: 9:00am-12:00am <br/>(416) 925-4334</td>
            </tr>
            <tr>
                <td class="one">Metro</td>
                <td class="two">425 Bloor St W<br/>Toronto, ON M5S 1X6</td>
                <td >Mon - Sun: 24 hours <br/>(416) 923-9099</td>
            </tr>
            <tr>
                <td class="one">Loblaws</td>
                <td class="two">60 Carlton St<br/>Toronto, ON M5B 1J1</td>
                <td >Mon - Sat: 7:00am-11:00pm <br/>(416) 593-6154</td>
            </tr>
        </table>

    </div>

    <div class="footer panel">
        <div id="names">Berkay Antmen<br/>Kamyar Ghasemipour<br/>Rahul Chaundry<br/>Marcus Tan</div>
    </div>
    </div>

<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/move.js"></script>

</body>
</html>
