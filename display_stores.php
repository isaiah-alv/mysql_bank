<?php
define("IN_CODE", 1);
include "dbconfig.php";

$con = mysqli_connect($host, $username, $password, $dbname)
    or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());

if (!isset($_COOKIE["customer_id"])) {
    echo "Please Login First";
    echo "<br><a href=\"index.html\">Back To Home</a>";
    die;
} else {
    echo "<html>\n<body style= 'font-family: 'Trebuchet MS', sans-serif;'>";
    echo "<strong>The following stores are in the database. </strong>";
    
    $store_query = "SELECT sid as ID, name as Name, address AS Address, 
    city as City, state as State, zipcode as Zipcode, 
    concat(longitude,' ',latitude) as 'Location(Latitude,Lognitude)'
    FROM CPS3740.Stores WHERE latitude IS NOT NULL";

    $store_result = mysqli_query($con, $store_query);
    $num_stores = mysqli_num_rows($store_result);
    if ($num_stores > 0) {
        echo "<TABLE border = 1>";
        echo "<TR><TH>ID<TH>Name<TH>Address<TH>City<TH>State<TH>Zipcode<TH>Location(Latitude,Lognitude)";
        while ($row = mysqli_fetch_array($store_result)) {
            $id = $row["ID"];
            $s_name = $row["Name"];
            $s_address = $row["Address"];
            $s_city = $row["City"];
            $s_state = $row["State"];
            $s_zipc = $row["Zipcode"];
            $s_location = $row["Location(Latitude,Lognitude)"];
            echo "<TR><TD>$id<TD>$s_name<TD>$s_address<TD>$s_city<TD>$s_state<TD>$s_zipc<td>$s_location";
        }
        echo "</table>";
    }
    mysqli_free_result($store_result);
}
mysqli_close($con);

?>