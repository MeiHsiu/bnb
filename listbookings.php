<!DOCTYPE HTML>
<html><head><title>Browse bookings</title> </head>
 <body>

<?php

    include "checksession.php";
    include "header.php";
    include "menu.php";
    echo '<div id="site_content">';
    include "sidebar.php";
    echo '<div id="content">';
    
    include "config.php"; //load in any variables
$DBC = mysqli_connect(SERVERNAME, DBUSER, DBPASSWORD, DBDATABASE);

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}

//prepare a query and send it to the server
$query = 'SELECT b.bookingID, r.roomname, b.checkindate, b.checkoutdate, c.firstname, c.lastname
FROM `booking` b, room r, customer c 
WHERE b.roomID = r.roomID and b.customerID = c.customerID';
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
?>
<h1>Cruuent bookings</h1>
<h2>
  <?php  if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']  ==1){?>
  <a href='addbooking.php'>[Make a booking]</a>
  <?php } ?>
  <a href="/bnb/">[Return to main page]</a>
</h2>
<table border="1">
<thead><tr><th>Booking (room, dates)</th><th>Customer</th><th>Action</th></tr></thead>
<?php

//makes sure we have bookings
if ($rowcount > 0) {  
    while ($row = mysqli_fetch_assoc($result)) {
	  $id = $row['bookingID'];	
	  echo '<tr><td>'.$row['roomname'].', '.$row['checkindate'].', '.$row['checkoutdate'].'</td><td>'.$row['firstname'].', '.$row['lastname'].'</td>';
	  echo     '<td><a href="viewbooking.php?id='.$id.'">[view]</a>';
	  echo         '<a href="editbooking.php?id='.$id.'">[edit]</a>';
	  echo         '<a href="editreview.php?id='.$id.'">[manage reviews]</a>';
	  echo         '<a href="deletebooking.php?id='.$id.'">[delete]</a></td>';
      echo '</tr>'.PHP_EOL;
   }
} else echo "<h2>No bookings found!</h2>"; //suitable feedback

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done
?>
</table>



<?php 
include "checksession.php";
        if (isset($_SESSION['username'])){
          if (isset($_POST['logout'])) logout();

          $un = $_SESSION['username'];
            if($_SESSION['loggedin'] == 1){ ?>
           
           
           <h6>Logged in as <?php echo $un ?></h6>
            
            
            <form method="post">
            <input  type="submit" name="logout" value="Logout"> 
            </form>

	
          <?php 
            }
        }
        ?>

<?php
    echo '</div></div>';
    include "footer.php";
?>

</body>
</html>
  