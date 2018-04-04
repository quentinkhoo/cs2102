<?php
//NOT TO BE CONFUSED WITH ADVERTISEMENTS.PHP, THIS FILE IS TO CREATE ADVERTISEMENTS

require_once 'config.php';

session_start();

date_default_timezone_set('Asia/Singapore');
 
//Define all the variables I need to use first
$origin = $dest = $pickuptime = $pickuphour = $pickupmin = $minbid = "";
$origin_err = $dest_err = $pickuptime_err = $pickuphour_err = $pickupmin_err = $minbid_err = $cars_err = "";

//Redirect to login page if session has not started
if (!isset($_SESSION['username']) || empty($_SESSION['username'] || !isset($_SESSION['userid']),
    empty($_SESSION['userid']))) {
  header("location: login.php");
  exit;
} else {
	//Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		
		//Validate origin
		if(empty(trim($_POST["origin"]))){
        	$origin_err = "Please enter the location you wish to start from. ";
    	} else {
        	$origin = trim($_POST["origin"]);
    	}

    	//Validate destination
    	if(empty(trim($_POST["dest"]))){
        	$dest_err = "Please enter your destination. ";
    	} else {
        	$dest = trim($_POST["dest"]);
    	}

    	//Validate input hour
    	if(empty(trim($_POST["pickuphour"]))){
	        $carseats_err = "Please choose an hour. ";
	    } else {
	        $carseats = trim($_POST["pickuphour"]);
	    }

	    //Validate input minute
	    if(empty(trim($_POST["pickupmin"]))){
	        $carseats_err = "Please choose a minute. ";
	    } else {
	        $carseats = trim($_POST["pickupmin"]);
	    }

	    //Convert into time interval for timestamp format
	    $pickuphour .= ":"; //add : behind the value of pickuphour
	    $pickupmin .= ":00"; //add :00 behind the value of pickupmin
	    $pickuptime = $pickuphour.$pickupmin; //get time in the following format: 00:00:00

		//Retrieve user from username to retrieve list of cars 
        $get_user_cars = "select * from car";
        $prepare_user = pg_prepare($db, "", $get_user_cars);    
        if ($prepare_user) {
			$execute_user = pg_execute($db, "", array());
			if (!$execute_user) {
				$user_err = "Something went wrong! Please try again.";
			} else {
                $num_rows = pg_num_rows($prepare_user);
                if ($num_rows == 0) {
                    $adverts_err = "There are no cars listed under your name. Please add a car before creating an advertisement.";
                }   
            }
		}

		// Check for input errors before inserting in database
	    if(empty($origin_err) && empty($dest_err) && empty($pickuptime_err) && empty($car_err)){
	        $sql_update_advertisement = "INSERT INTO person (origin, destination, pickuptime, car) 
	                        VALUES ($1, $2, $3, $4, $5)";
	        $prepare_update_advertisement = pg_prepare($db, "", $sql_update_advertisement);
	    }
	}
}
?>

<!DOCTYPE html>
<html lang = en>
<head>
	<meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
	<div class = "wrapper">
		<h2>Create an Advertisement Today!</h2>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

			<div class="form-group <?php echo (!empty($origin_err)) ? 'has-error' : ''; ?>">
                <label>I am travelling from</label>
                <input type="text" name="origin"class="form-control" value="<?php echo $origin; ?>">
                <span class="help-block"><?php echo $origin_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($dest_err)) ? 'has-error' : ''; ?>">
                <label>To</label>
                <input type="text" name="dest"class="form-control" value="<?php echo $dest; ?>">
                <span class="help-block"><?php echo $dest_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($pickuptime_err)) ? 'has-error' : ''; ?>">
                <label>At time (in 00:00 format):</label>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group <?php echo (!empty($pickuptime_err)) ? 'has-error' : ''; ?>">
                        	<label>Hour</label>
                            <select name="pickuphour" class="form-control" value="<?php echo $pickuphour; ?>">
                                <option value="00">12AM</option>
                                <option value="01">1AM</option>
                                <option value="02">2AM</option>
                                <option value="03">3AM</option>
                                <option value="04">4AM</option>
                                <option value="05">5AM</option>
                                <option value="06">6AM</option>
                                <option value="07">7AM</option>
                                <option value="08">8AM</option>
                                <option value="09">9AM</option>
                                <option value="10">10AM</option>
                                <option value="11">11AM</option>
                                <option value="12">12PM</option>
                                <option value="13">1PM</option>
                                <option value="14">2PM</option>
                                <option value="15">3PM</option>
                                <option value="16">4PM</option>
                                <option value="17">5PM</option>
                                <option value="18">6PM</option>
                                <option value="19">7PM>/option>
                                <option value="20">8PM</option>
                                <option value="21">9PM</option>
                                <option value="22">10PM</option>
                                <option value="23">11PM</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                        	<label>Minute</label>
                            <select name="pickupmin" class="form-control" value="<?php echo $pickupmin; ?>">
                            	<option value="00">00</option>
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                                <option value="25">25</option>
                                <option value="26">26</option>
                                <option value="27">27</option>
                                <option value="28">28</option>
                                <option value="29">29</option>
                                <option value="30">30</option>
                                <option value="31">31</option>
                                <option value="32">32</option>
                                <option value="33">33</option>
                                <option value="34">34</option>
                                <option value="35">35</option>
                                <option value="36">36</option>
                                <option value="37">37</option>
                                <option value="38">38</option>
                                <option value="39">39</option>
                                <option value="40">40</option>
                                <option value="41">41</option>
                                <option value="42">42</option>
                                <option value="43">43</option>
                                <option value="44">44</option>
                                <option value="45">45</option>
                                <option value="46">46</option>
                                <option value="47">47</option>
                                <option value="48">48</option>
                                <option value="49">49</option>
                                <option value="50">50</option>
                                <option value="51">51</option>
                                <option value="52">52</option>
                                <option value="53">53</option>
                                <option value="54">54</option>
                                <option value="55">55</option>
                                <option value="56">56</option>
                                <option value="57">57</option>
                                <option value="58">58</option>
                                <option value="59">59</option>
                            </select>
                        <span class="help-block"><?php echo $pickuptime_err; ?></span>
                    </div>
                </div>
            </div>

            <div class="form-group <?php echo (!empty($minbid_err)) ? 'has-error' : ''; ?>">
                <label>I would require a minimum bid of</label>
                <input type="text" name="minbid"class="form-control" value="<?php echo $minbid; ?>">
                <span class="help-block"><?php echo $minbid_err; ?></span>
            </div>

            <label>The car I would like to use is</label>
            <table style="width:100%">
            
			<tr>
				<th><p>Car ID</p></th>
				<th><p>Model</p></th>
				<th><p>Colour</p></th>
				<th><p>License</p></th>
				<th><p>Seats</p></th>
			</tr>
            <?php echo (!empty($cars_err)) ? $cars_err : ''; ?> 
            <?php while($row = pg_fetch_assoc($execute_user)) { ?>
		  	<tr>
			<td><?php echo $row[carid]; ?></td>
			<td><?php echo $row[model]; ?></td>
			<td><?php echo $row[colour]; ?></td>
			<td><?php echo $row[license]; ?></td>
			<td><?php echo $row[seats]; ?></td>
			<td>
				<div class="form-group <?php echo (!empty($cars_err)) ? 'has-error' : ''; ?>">				
					<input type="text" name="car"class="form-control" value="<?php echo $car; ?>">
				</div>
				<input name="carid" type="hidden" value="<?php echo $row[carid]; ?>" />
				<input name="model" type="hidden" value="<?php echo $row[model]; ?>" />
				<input name="colour" type="hidden" value="<?php echo $row[colour]; ?>" />
				<input name="license" type="hidden" value="<?php echo $row[license]; ?>" />
				<input name="seats" type="hidden" value="<?php echo $row[seats]; ?>" />
			</td>
			</tr>
		<?php } ?> 
		</form>
	</div>
    <p><a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a></p>
</body>
</html>