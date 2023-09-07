 <!DOCTYPE HTML>
<html>
    <head>
        <title>Add a new booking</title> 
        <link
        rel="stylesheet"
        href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"
        />
        <!-- <link rel="stylesheet" href="/resources/demos/style.css"> -->
        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    </head>
 <body>

<?php
//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

include "config.php"; //load in any variables
$DBC = mysqli_connect(SERVERNAME, DBUSER, DBPASSWORD, DBDATABASE);

//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {
  $error = 0; //clear our error flag
  $msg = 'Error: ';  
//if ($_SERVER["REQUEST_METHOD"] == "POST") { //alternative simpler POST test    

    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
        exit; //stop processing the page further
    };
    $customerID = 1;
//validate incoming data - only the first field is done for you in this example - rest is up to you do
//roomID
        $roomID = $_POST['roomID'];        
//customerID
        $customerID = 1;        
//checkindate
        $checkindate = date_format(date_create($_POST['checkindate']),"Y-m-d");        
//checkoutdate
        $checkoutdate = date_format(date_create($_POST['checkoutdate']),"Y-m-d");         
//contactnumber
        $contactnumber = cleanInput($_POST['contactnumber']);         
//bookingextras
        $bookingextras = cleanInput($_POST['bookingextras']);         
//check check in and check out date periord
        if ($checkindate >=$checkoutdate){
          $error++;
          $msg .="Check-out date cannot be earlier than or equal to check-in date ";
        }

//save the booking data if the error flag is still clear
    if ($error == 0) {
        $query = "INSERT INTO booking (roomid,customerid,checkindate,checkoutdate,contactnumber,bookingextras) VALUES (?,?,?,?,?,?)";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'iissss', $roomID, $customerID, $checkindate, $checkoutdate,$contactnumber,$bookingextras); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>New booking added to the list</h2>";        
    } else { 
      echo "<h2><font color='red'>$msg</font></h2>".PHP_EOL;
    }      
}

$query = 'SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomID';
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

mysqli_close($DBC); //close the connection once done

?>
<h1>Add a new booking</h1>
<h2><a href='listbookings.php'>[Return to the booking listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>

<form method="POST" >
  <p>
    <label for="roomID">Room (name,type,beds): </label>
    <select id="roomID" name="roomID" required> 
    <?php
      if ($rowcount > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
              $id = $row['roomID']; ?>

              <option value="<?php echo $row['roomID']; ?>">
                  <?php echo $row['roomname'] . ', '
                      . $row['roomtype'] . ', '
                      . $row['beds'] ?>
              </option>
      <?php }
      } else echo "<option>No flights found</option>";
      mysqli_free_result($result);
    ?>
    </select>
  </p> 
  <p>
    <label for="checkindate">Checkin Date: </label>
    <input type="text" id="checkindate" name="checkindate" value="" require >
  </p>  
  <p>  
    <label for="checkoutdate">Checkout Date: </label>
    <input type="text" id="checkoutdate" name="checkoutdate" value="" require >
   </p>
   <p>
    <label for="contactnumber">Contact number: </label>
    <input type="tel" id="contactnumber" name="contactnumber" value="" required placeholder="(001) 123-1234" pattern="[\(]\d{3}[\)] \d{3}-\d{4}"> 
  </p>  
  <p>
    <label for="bookingextras">Booking Extras: </label>
    <textarea id="bookingextras" name="bookingextras" size="200" minlength="0" maxlength="200" rows="5" cols="50" ></textarea>
  </p>    
   <input type="submit" name="submit" value="Add">
   <a href="listbookings.php">[Cancel]</a>

  </form>

   <form id="searchForm" method="POST" action="filterroom.php">
   <hr>
   <h2>Search for room availability</h2>
   <p>
    <label for="fromDate">Start Date: </label>
    <input type="text" id="fromDate" name="sqa" value="" require >
    <label for="toDate">End Date: </label>
    <input type="text" id="toDate" name="sqb" value="" require >
    <input type="submit" name="search" id="search" value="Search availability">

    <table id="result" border ="1"></table>
    </form>
</p>

<br><br>

<div class="row">
    <table id="tblbookings" border="1">
        <thead>
            <tr>
              <th>Room #</th>
              <th>Room Name</th>
              <th>Room Type</th>
              <th>Beds</th>
            </tr>
        </thead>
    </table>
</div>

</body>


<script>
    $("#checkindate").datepicker({
      numberOfMonths: 1,
      changeYear: true,
      changeMonth: true,
      showWeek: true,
      weekHeader: "Weeks",
      showOtherMonths: true,
      minDate: 0,
      //   maxDate: new Date(2024, 1, 1),
      yearRange: "2023:2024",
      dateFormat: 'dd-mm-yy',
    });
    $("#checkoutdate").datepicker({
      numberOfMonths: 1,
      changeYear: true,
      changeMonth: true,
      showWeek: true,
      weekHeader: "Weeks",
      showOtherMonths: true,
      minDate: 0,
      //   maxDate: new Date(2024, 1, 1),
      yearRange: "2023:2024",
      dateFormat: 'dd-mm-yy',
    });
    $("#fromDate").datepicker({
      numberOfMonths: 1,
      changeYear: true,
      changeMonth: true,
      showWeek: true,
      weekHeader: "Weeks",
      showOtherMonths: true,
      minDate: 0,
      //   maxDate: new Date(2024, 1, 1),
      yearRange: "2023:2024",
      dateFormat: 'dd-mm-yy',
    });
    $("#toDate").datepicker({
      numberOfMonths: 1,
      changeYear: true,
      changeMonth: true,
      showWeek: true,
      weekHeader: "Weeks",
      showOtherMonths: true,
      minDate: 0,
      //   maxDate: new Date(2024, 1, 1),
      yearRange: "2023:2024",
      dateFormat: 'dd-mm-yy',
    });
  </script>

<script>
        $(document).ready(function() {
				
            $('#searchForm').submit(function(event) {
                var formData = {
                    sqa: $('#fromDate').val(),
                    sqb: $('#toDate').val()
                };
                $.ajax({
                    type: "POST",
                    url: "filterroom.php",
                    data: formData,
                    dataType: "json",
                    encode: true,

                }).done(function(data) {
                    var tbl = document.getElementById("tblbookings"); //find the table in the HTML  
                    var rowCount = tbl.rows.length;

                    for (var i = 1; i < rowCount; i++) {
                        //delete from the top - row 0 is the table header we keep
                        tbl.deleteRow(1);
                    }

                    //populate the table
                    //data.length is the size of our array

                    for (var i = 0; i < data.length; i++) {
                        var fid = data[i]['roomID'];
                        var fn = data[i]['roomname'];
                        var dl = data[i]['roomtype'];
                        var tl = data[i]['beds'];
                        //create a table row with four cells
                        //Insert new cell(s) with content at the end of a table row 
                        //https://www.w3schools.com/jsref/met_tablerow_insertcell.asp  
                        tr = tbl.insertRow(-1);
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = fid; //flightID
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = fn; //flight name  
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = dl; //departure location       
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = tl; //destination location         
                    }
                });
                event.preventDefault();
            })
        })
</script>

</html>
  