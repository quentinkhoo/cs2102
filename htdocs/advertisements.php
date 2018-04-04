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
	$get_adverts_record = "SELECT * FROM ride WHERE status = 'open'";
	$prepare_adverts = pg_prepare($db, "", $get_adverts_record);
	
    if ($prepare_adverts) {
		$execute_adverts = pg_execute($db, "", array());
		if (!$execute_adverts) {
			$adverts_err = "Something went wrong! Please try again.";
		} else {
			$num_rows = pg_num_rows($execute_adverts);
			if ($num_rows == 0) {
				$adverts_err = "There are no rides available.";
			}				
		}
	}
	else {
		$adverts_err = "SQL statement cannot be prepared :(";
	}
	
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if(empty(trim($_POST["bid"]))) {
	        $bids_err = 'Please enter bid.';
	    } else if (!is_numeric(trim($_POST['bid']))) {
	    	$bids_err = 'Only numeric input is allowed.';
	    } else if (trim($_POST["bid"]) < substr(trim($_POST["minbid"]), 2, strlen($_POST["minbid"]) - 5)) {
	    	$bids_err = 'Minimum bid is ' . substr(trim($_POST["minbid"]), 1);
	    } else {
	        $userbid = trim($_POST["bid"]);
	        $bidTime = trim($_POST["bidtime"]);
        	$bidID = trim($_POST["bidID"]);
        	$bidorigin = trim($_POST["origin"]);
        	$biddest = trim($_POST["dest"]);
        	$minbid = trim($_POST["minbid"]);
	    }

        $adID = trim($_POST["adID"]);
        $bidpickuptime = trim($_POST["pickuptime"]);
		
		$insert_bid = "INSERT INTO bid VALUES ($1,$2,$3,$4,$5,$6,$7,$8)";
		$prepare_bid = pg_prepare($db, "", $insert_bid);
		
		if ($prepare_bid) {
			$execute_bid = pg_execute($db, "", array($userbid, $bidTime, $bidID, $adID, $bidorigin, $biddest, $bidpickuptime, $minbid));
			if (!$execute_bid) {
				if (!$bids_err) {
					$bids_err = "You have already bidded for this ride. Please go to 'Manage Bid' to edit your bid.";
				}
			} else {
				echo "Bid Successfully!";
			}
		}
		else {
			$bids_err = "Unable to perform bid.";
		}
	}

	// Reset the variables
	$userbid = "";
	$bidorigin = "";
	$biddest = "";
	$minbid = "";
	$bidID = "";
	$bidTime = "";
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
		table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
			table-layout: fixed;
			width: 80px;
		}
		th, td {
			padding: 10px;
		}
		
		input[type="text"] {
 		   width: 80px;
 		   height: 30px;
		}
		
		p {text-align:center;}
		td {text-align:center;}
    </style>
</head>
<body>
    <div class="page-header">
        <h1><b>List of Rides</b></h1>
		<table style="width:100%">
		<tr>
			<th><p text-align:"center">Origin</p></th>
			<th><p>Destination</p></th>
			<th><p>Pick Up Time</p></th>
			<th><p>Minimum Bid</p></th>
			<th><p>Bid Here</p></th>
		</tr>
		<?php echo (!empty($adverts_err)) ? $adverts_err : ''; ?>
		<?php while($row = pg_fetch_assoc($execute_adverts)) { ?>
			<tr>
			<td><?php echo $row[origin]; ?></td>
			<td><?php echo $row[dest]; ?></td>
			<td><?php echo $row[pickuptime]; ?></td>
			<td><?php echo $row[minbid]; ?></td>
			<td>
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<div class="form-group <?php echo (!empty($bids_err) && $row[advertiserid] == $adID && $row[pickuptime] == $bidpickuptime) ? 'has-error' : ''; ?>">					
						<input type="submit" value="Bid" class="btn btn-primary" style="float: right" />
  						<div style="overflow: hidden; padding-right: .25em;">
    						<input type="text" name="bid" value="" maxlength="5" length="5" class="form-control" style="width: 100%;" />
    						<span class="help-block"><?php echo ($row[advertiserid] == $adID && $row[pickuptime] == $bidpickuptime) ? $bids_err : ""; ?></span>
   						</div>
					</div>
					<input name="origin" type="hidden" value="<?php echo $row[origin]; ?>" />
					<input name="dest" type="hidden" value="<?php echo $row[dest]; ?>" />
					<input name="pickuptime" type="hidden" value="<?php echo $row[pickuptime]; ?>" />
					<input name="minbid" type="hidden" value="<?php echo $row[minbid]; ?>" />
					<input name="adID" type="hidden" value="<?php echo $row[advertiserid]; ?>" />
					<input name="bidID" type="hidden" value="<?php echo $_SESSION['userid']; ?>" />
					<input name="bidtime" type="hidden" value="<?php echo date("Y-m-d H:i:s") ?>" />
				</form>
			</td>
			</tr>
		<?php } ?>
		</table>
		<?php echo date("Y-m-d H:i:s") ?>
    </div>
    <p><a href="welcome.php" class="btn btn-warning">Go Back</a></p>
    <p><a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a></p>
</body>
</html>