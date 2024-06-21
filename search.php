<?php
define("IN_CODE", 1);
require_once "dbconfig.php";

// Display logout link
echo "<a href='logout.php'>Logout</a>";

// Establish database connection
$con = mysqli_connect($host, $username, $password, $dbname);
if (!$con) {
    die("<br>Cannot connect to DB: $dbname on $host, error: " . mysqli_connect_error());
}

// Ensure the user is logged in
if (!isset($_COOKIE["customer_id"])) {
    echo "Please Login First!";
    exit;
}

$name = $_COOKIE["customer_name"];
$id = $_COOKIE["customer_id"];

// Start HTML output
echo "<html>\n<body style='font-family: Trebuchet MS, sans-serif;'>";

// Check for keyword input
if (!isset($_GET["keyword"])) {
    echo "<br> Please enter a keyword";
} else {
    $keyword = $_GET["keyword"];
    echo "<p>The transactions in the customer <strong>$name</strong> records matched keyword: <strong>$keyword</strong> are:</p>";

    // Prepare query based on keyword
    $query = $keyword === "*" ?
        "SELECT mid AS ID, code AS Code, type AS Type, amount AS Amount, Sources.name AS Source, mydatetime AS DateTime, note AS Note FROM CPS3740_2022F.Money_alviolai INNER JOIN CPS3740.Sources ON Money_alviolai.sid = Sources.id WHERE cid = '$id'" :
        "SELECT mid AS ID, code AS Code, type AS Type, amount AS Amount, Sources.name AS Source, mydatetime AS DateTime, note AS Note FROM CPS3740_2022F.Money_alviolai INNER JOIN CPS3740.Sources ON Money_alviolai.sid = Sources.id WHERE note LIKE '%$keyword%' AND cid = '$id'";

    $result = mysqli_query($con, $query);
    $num_search = mysqli_num_rows($result);

    if ($num_search > 0) {
        echo "<table border='1'><tr><th>ID</th><th>Code</th><th>Type</th><th>Amount</th><th>Source</th><th>Date & Time</th><th>Note</th>";
        $total_balance = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $transaction_amount = ($row["Type"] === "W") ? -$row["Amount"] : $row["Amount"];
            $color = $transaction_amount < 0 ? "red" : "blue";
            $total_balance += $transaction_amount;
            
            echo "<tr style='color: $color'><td>{$row['ID']}</td><td>{$row['Code']}</td><td>{$row['Type']}</td><td>{$transaction_amount}</td><td>{$row['Source']}</td><td>{$row['DateTime']}</td><td>{$row['Note']}</td></tr>";
        }
        echo "</table>";
        echo "Total Balance: <span style='color: " . ($total_balance < 0 ? "red" : "blue") . "'>$total_balance</span>";
    } else {
        echo "<br>No Transactions With Keyword: <strong>$keyword</strong>";
    }
    mysqli_free_result($result);
}

echo "</body>\n</html>";
mysqli_close($con);
?>
