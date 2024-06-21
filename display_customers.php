<?php

// Define constant for code inclusion check
define("IN_CODE", 1);

// Include database configuration
include "dbconfig.php";

// Connect to the database
$con = mysqli_connect($host, $username, $password, $dbname) 
    or die("<br>Cannot connect to DB:$dbname on $host, error: ".mysqli_connect_error());

// Query to fetch all customers
$sql = "SELECT * FROM CPS3740.Customers";
$result = mysqli_query($con, $sql); 

// Display heading for customer list
echo "<br>The following customers are in the bank system:\n";

if ($result) {
    // Check if there are rows returned
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1'>\n";
        echo "<tr><th>ID</th><th>Login</th><th>Password</th><th>Name</th><th>Gender</th><th>DOB</th><th>Street</th><th>City</th><th>State</th><th>Zipcode</th></tr>";
        while ($row = mysqli_fetch_array($result)) {
            $id = $row["id"];
            $login = $row["login"];
            $password = $row["password"];
            $name = $row["name"];
            $gender = $row["gender"];
            $dob = $row["DOB"];
            $street = $row["street"];
            $city = $row["city"];
            $state = $row["state"];
            $zipcode = $row["zipcode"];
            echo "<tr><td>$id</td><td>$login</td><td>$password</td><td>$name</td><td>$gender</td><td>$dob</td><td>$street</td><td>$city</td><td>$state</td><td>$zipcode</td></tr>";
        }
        echo "</table>\n";
    } else {
        echo "<br>No record found\n";
    }
} else {
    echo "Something is wrong with SQL:" . mysqli_error($con);	
}

// Clean up: free result and close connection
mysqli_free_result($result);
mysqli_close($con);


?>
