<?php
// Include config file
require_once 'config.php';

// Initialize the session
session_start();

// Set the default timezone to use
date_default_timezone_set('Asia/Singapore');
 
// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: login.php");
  exit;
} else {
	$user_bids_err = $bids_err = "";
	$get_user_bid_records = "SELECT origin, dest, pickuptime, minbid, fullname, username, bidamt, phone, status FROM bid INNER JOIN person ON bidderid = userid NATURAL JOIN ride WHERE advertiserid = $1 ";
	$prepare_user_bids = pg_prepare($db, "", $get_user_bid_records);
	
    if ($prepare_user_bids) {
		$execute_user_bids = pg_execute($db, "", array($_SESSION['userid']));
		if (!$execute_user_bids) {
			$user_bids_err = "Something went wrong! Please try again.";
		} else {
			$num_rows = pg_num_rows($execute_user_bids);
			if ($num_rows == 0) {
				$bids_err = "There are no rides available.";
			}				
		}
	}
	else {
		$user_bids_err = "SQL statement cannot be prepared :(";
	}
	
}
?>
 
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<style>
	table {
    	width:95%;
	}
	table, th, td {
	    border: 1px solid black;
	    border-collapse: collapse;
	}
	th, td {
	    padding: 5px;
	    text-align: left;
	}
	table#t01 tr:nth-child(even) {
	    background-color: #eee;
	}
	table#t01 tr:nth-child(odd) {
	   background-color:#fff;
	}
	table#t01 th {
	    background-color: black;
	    color: white;
	    text-align:center;
	}

	h2 {
	    display: block;
	    font-size: 1.5em;
	    margin-top: 0.83em;
	    margin-bottom: 0.83em;
	    margin-left: 0;
	    margin-right: 0;
	    font-weight: bold;
	    text-align: center;
	}

	p {text-align:center;}
	td {text-align:center;}
	</style>
</head>
<body>

<h2>Your Advertisements</h2>

<table id = "t01" align ="center">
  <tr>
    <th>Origin</th>
    <th>Destination</th>
    <th>Time</th>
    <th>Minimum Bid</th>
    <th>Bidder's Full Name</th>
    <th>Bidder's Username</th>
    <th>Bidder's Bid</th>
    <th>Bidder's Contact</th>
    <th>Status</th>
  </tr>
  <?php echo (!empty($user_bids_err)) ? $user_bids_err : ''; ?>
  <?php while($row = pg_fetch_assoc($execute_user_bids)) { ?>
  <tr>
    <td><?php echo $row[origin]; ?></td>
    <td><?php echo $row[dest]; ?></td>
    <td><?php echo $row[pickuptime]; ?></td>
    <td><?php echo $row[minbid]; ?></td>
    <td><?php echo $row[fullname]; ?></td>
    <td><?php echo $row[username]; ?></td>
    <td><?php echo $row[bidamt]; ?></td>
    <td><?php echo $row[phone]; ?></td>
    <td><?php echo $row[status]; ?></td>
  </tr>
  <?php }?>
</table>
<br>
<br>
<p><a href="welcome.php" class="btn btn-warning">Go Back</a></p>
<p><a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a></p>

</body>
</html>