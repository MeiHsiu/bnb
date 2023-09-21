<?php
include "ChromePhp.php";
// Database connection settings
include "config.php"; //load in any variables

// Create a new database connection
$conn = new mysqli(SERVERNAME, DBUSER, DBPASSWORD, DBDATABASE) or die();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the from date and to date from the query string
$fromDate = date_format(date_create($_POST['sqa']),"Y-m-d");
$toDate = date_format(date_create($_POST['sqb']),"Y-m-d");

ChromePhp::log($fromDate);
ChromePhp::log($toDate);

$searchresult ='';
if(true){

// Prepare the SQL query to search for tickets between the from date and to date
$query = "SELECT * FROM room 
        WHERE roomid not in 
        (SELECT roomid FROM booking 
         WHERE checkindate <= '$toDate' AND checkoutdate >= '$fromDate')";

$result =mysqli_query($conn,$query);
$rowcount = mysqli_num_rows($result);
if($rowcount > 0){
    $rows=[];  //start an empty array
    while ($row = mysqli_fetch_assoc($result)){
        $rows[]= $row;
    }

    ChromePhp::log($rows);

    //take the array of our 1 or more bookings and turn it into a JSON text
    $searchresult = json_encode($rows);

    header('Content-Type: text/json; charset=utf-8');
}else echo "<tr><td><h5>No bookings found!</h5></td></tr>";

}else echo "<tr><td><h5>Invalid search query</h5></td></tr>";

mysqli_free_result($result);
mysqli_close($conn);
echo $searchresult;

?>