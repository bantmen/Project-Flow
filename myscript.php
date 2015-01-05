// <?php
// $con=mysqli_connect("localhost","flowit","Spring300","FlowDB");
// if (mysqli_connect_errno($con))
// {
   // echo "Failed to connect to MySQL: " . mysqli_connect_error();
// }
// $query = $_GET['query'];
// if ($result = $con->query($query)) {

    // /* fetch associative array */
    // while ($row = $result->fetch_assoc()) {
        // printf ("%s (%s)\n", $row["Name"], $row["CountryCode"]);
    // }

    // /* free result set */
    // $result->free();
// }
// $con->close();
// ?>

<?php
$mysqli = new mysqli("localhost", "flowit", "Spring300", "FlowDB");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

//$query = "SELECT Name, CountryCode FROM City ORDER by ID DESC LIMIT 50,5";
$query = "SELECT tran_id FROM Transaction";

if ($result = $mysqli->query($query)) {

    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
        printf ("%s (%s)\n", $row["Name"], $row["CountryCode"]);
    }

    /* free result set */
    $result->free();
}

/* close connection */
$mysqli->close();
?>