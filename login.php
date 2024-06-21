<?php
define("IN_CODE", 1);
require_once "dbconfig.php";

header('Content-Type: text/html; charset=utf-8');
echo "<html><body style='font-family: Trebuchet MS, sans-serif;'>";

// Simple login check
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    echo "Please Login First<br><a href='index.html'>Back To Home</a>";
    exit;
}

// Database connection
$con = mysqli_connect($host, $username, $password, $dbname);
if (!$con) {
    die("<br>Cannot connect to DB: $dbname on $host, error: " . mysqli_connect_error());
}

// Prevent SQL injection
$login = mysqli_real_escape_string($con, $_POST['username']);
$bpassword = mysqli_real_escape_string($con, $_POST['password']);

// Login query
$check_login = "SELECT id, name, gender, img, DOB, street, city, state, zipcode, password FROM CPS3740.Customers WHERE login = '$login'";
$result = mysqli_query($con, $check_login);

if (!$result) {
    echo "Error description: " . mysqli_error($con);
    exit;
}

if (mysqli_num_rows($result) === 0) {
    echo "<br>'$login' does not exist in the database.";
} else {
    $user = mysqli_fetch_assoc($result);

    if ($user['password'] !== $bpassword) {
        echo "<br>Login '$login' exists, but passwords do not match.";
    } else {
        authenticateUser($user);
    }
}

mysqli_close($con);
echo "</body></html>";

function authenticateUser($user) {
    // Setting cookies
    setcookie("customer_id", $user['id'], time() + 84000, "/");
    setcookie("customer_name", $user['name'], time() + 84000, "/");

    // Welcome message
    echo "Welcome Customer: <strong>{$user['name']}</strong>";

    // User's age
    $today = date("Y-m-d");
    $age = date_diff(date_create($user['DOB']), date_create($today))->format('%y');
    echo "<br>Your Age: $age";

    // User's address
    echo "<br>Your Address Is: {$user['street']}, {$user['city']}, {$user['zipcode']}";

    // Display user's image
    echo "<br><img src='data:image/jpeg;base64," . base64_encode($user['img']) . "'>";

    // Additional user info
    displayAdditionalUserInfo();

    // Transaction information
    displayTransactionInfo($user,$user['id']);
}

function displayAdditionalUserInfo() {
    $ip = $_SERVER['REMOTE_ADDR'];
    echo "<br> Your IP: $ip";
    $ipv4 = explode(".", $ip);
    $location = (($ipv4[0] == "131" && $ipv4[1] == "125") || $ipv4[0] == "10") ? "at Kean University." : "not at Kean University.";
    echo "<br>You are $location";
    echo "<br>Your Browser and OS: " . $_SERVER['HTTP_USER_AGENT'] . "<hr>";
}

function displayTransactionInfo($user, $customerId) {
    global $con;
    $query = "SELECT mid AS ID, code AS Code, type AS Type, amount AS Amount, Sources.name AS Source, mydatetime AS DateTime, note AS Note FROM CPS3740_2022F.Money_alviolai INNER JOIN CPS3740.Sources ON Money_alviolai.sid = Sources.id WHERE cid = '$customerId' ORDER BY mid ASC";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        echo "There are <strong>" . mysqli_num_rows($result) . "</strong> transactions for <strong>{$user['name']}</strong>.<table border='1'>";
        $total_balance = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $color = $row['Type'] == "W" ? "red" : "blue";
            $amount = $row['Type'] == "W" ? -$row['Amount'] : $row['Amount'];
            $total_balance += $amount;
            echo "<tr style='color:$color'><td>{$row['ID']}</td><td>{$row['Code']}</td><td>{$row['Type']}</td><td>$amount</td><td>{$row['Source']}</td><td>{$row['DateTime']}</td><td>{$row['Note']}</td></tr>";
        }
        echo "</table>Total Balance: <strong style='color:" . ($total_balance < 0 ? "red" : "blue") . "'>$total_balance</strong>";
    } else {
        echo "<br>No Transactions Made: Not Updatable";
    }
    echo "<br><form action='add_transaction.php' method='post'>
            <input type='hidden' name='total_balance' value='$total_balance'/>
            <input type='submit' value='Add Transaction'>
            &emsp;<a href='display_transaction.php'>Display and update transaction</a>
            &emsp;<a href='display_stores.php'>Display stores</a>
          </form>";
    echo "<form action='search.php' method='get'>Keyword: <input type='text' name='keyword'><button type='submit'>Search Transaction</button></form>";
}
?>
