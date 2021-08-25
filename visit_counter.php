
<html>
    <head>
        <title>Visitor Counter</title>
        <style>
            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }

            tr:nth-child(even) {
                background-color: #dddddd;
            }
        </style>
    </head>
    <body>
<?php

// Starting session
session_start();

// Connecting with SQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "visitcounter";
$table = "visitor_counter";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

// Checking for table
if ($result = $conn->query("SHOW TABLES LIKE '$table'")) {
    if($result->num_rows == 1) {
        //PASS
    }
}
else {
    $crtTable = "CREATE TABLE $table (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        visit VARCHAR(30) NOT NULL,
        browser VARCHAR(30) NOT NULL,
        visit_time VARCHAR(50)
    )";

    if ($conn->query($crtTable) === TRUE) {
        // PASS
    } else {
        echo "Error creating $table: " . $conn->error;
    }
}

// Counting Views
$views = "";
if(isset($_SESSION['views'])){
    $_SESSION['views'] = $_SESSION['views']+1;
    $views = $_SESSION['views'];
    $views = $conn -> real_escape_string($views);
}else{
    $_SESSION['views']=1;
    $views = $_SESSION['views'];
    $views = $conn -> real_escape_string($views);
}

// Getting Visitor Browser
$arr_browsers = ["Opera", "Edg", "Chrome", "Safari", "Firefox", "MSIE", "Trident"];
$agent = $_SERVER['HTTP_USER_AGENT'];

$user_browser = '';
foreach ($arr_browsers as $browser) {
    if (strpos($agent, $browser) !== false) {
        $user_browser = $conn -> real_escape_string($browser);
        break;
    }   
}
switch ($user_browser) {
    case 'MSIE':
        $user_browser = $conn -> real_escape_string('Internet Explorer');
        break;
    case 'Trident':
        $user_browser = $conn -> real_escape_string('Internet Explorer');
        break;
    case 'Edg':
        $user_browser = $conn -> real_escape_string('Internet Explorer');
        break;
}

// Getting Visit time
$date = date("D M j G:i:s T Y");
$date = $conn -> real_escape_string($date); 

// Insert Data To Database
$sql = "INSERT INTO visitor_counter (visit, browser, visit_time) VALUES ('$views', '$user_browser', '$date')";

if($conn->query($sql)){
    // PASS
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
}

// Getting Data From Table
$data = "SELECT * FROM $table";
$result = $conn->query($data);

$total=mysqli_num_rows($result);


if ($result->num_rows > 0) { ?>
	 <td><h1 style="color:royalblue; border:2px solid black; margin: 5px auto 5px auto; border-radius: 50%; text-align: center!important; padding:10px; ;height:100px;width:100px;"><span class="spans" style="position: relative;
    top: 30px;"><?php echo $total;?>+</span></h1></td>
    <table>
        <tr>
            <th>Visit count</th>
            <th>User-agent</th>
            <th>Time of latest visit</th>
        </tr>
<?php
  while($row = $result->fetch_assoc()) { ?>
        <tr>
        	<td><?php echo $row["visit"]; ?></td>
            <td><?php echo $row["browser"]; ?></td>
            <td><?php echo $row["visit_time"]; ?></td>
           
        </tr>
  <?php
  }
  ?>
  </table>
  <?php
} else {
  echo "0 results";
}

$conn->close();    
?>
    </body>
</html>