<?php
define("IN_CODE", 1);
include "dbconfig.php";
echo "<html>\n<body style=\"font-family: 'Trebuchet MS', sans-serif;\">";
echo "<a href=\"logout.php\">Logout</a> <br> Only the <strong>Note</strong> field can be edited (Highlighted in Yellow)";
$con = mysqli_connect($host, $username, $password, $dbname)
    or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());

if (!isset($_COOKIE["customer_id"])) {
    echo "Please Login First";
    echo "<br><a href=\"index.html\">Back To Home</a>";
    die;
} else {
    $id = $_COOKIE['customer_id'];
    $transaction_table_query = "SELECT mid AS ID, code AS Code, type AS Type, 
    amount AS Amount, Sources.name AS Source, mydatetime AS DateTime, note AS Note 
    FROM CPS3740_2022F.Money_alviolai 
    INNER JOIN CPS3740.Sources ON Money_alviolai.sid = Sources.id 
    WHERE cid = '$id' 
    ORDER BY mid ASC";

    $result = mysqli_query($con, $transaction_table_query);
    $num_transactions = mysqli_num_rows($result);

    $total_balance = 0;
    if ($num_transactions > 0) {
        echo "<TABLE border = 1>";
        echo "<TR><TH>ID<TH>CODE<TH>Type<TH>Amount<TH>Source<TH>Date & Time<TH>Note<TH>Delete";
        $i = 0;
        while ($row = mysqli_fetch_array($result)) {

            $m_id = $row["ID"];
            $transaction_code = $row["Code"];
            $transaction_type = $row["Type"];
            $transaction_amount = $row["Amount"];
            $transation_source = $row["Source"];
            $transaction_timestamp = $row["DateTime"];
            $note = $row["Note"];


            if ($transaction_type == "W") {
                $transaction_amount = 0 - $transaction_amount;
            }
            if ($transaction_amount < 0) {
                $color = "red";
            } else {
                $color = "blue";
            }
            $total_balance = $total_balance + $transaction_amount;

            echo "
                <form action = 'update_transaction.php' method='post'>
                    
                    <input type='hidden'  name='note[$i]' value='$note'/>
                    <input type='hidden'  name='code[$i]' value='$transaction_code'/>
                    <TR><TD>$m_id<TD>$transaction_code<TD>$transaction_type<TD><font color = '$color'>$transaction_amount<TD>$transation_source<TD>$transaction_timestamp<TD><input type='text' name='update[$i]' value='$note' /><TD><input type='checkbox' name='delete[$i]' value='$transaction_code'/>";
            
            $i++;
        }
        echo "</table>";

        if ($total_balance < 0) { $color = "red"; } 
        else { $color = "blue"; }

        echo "Total Balance:<font color = '$color'>$total_balance";
        echo "
            <input type='hidden'  name='num_transactions' value='$num_transactions'/>
            <br><input type= 'submit' value= 'Update Transaction'/>
            </form>
        ";

    }

}
echo "<style> input[type= text]{ background-color: #FFFF00; } </style></body>\n</html>";
mysqli_free_result($result);
mysqli_close($con);
?>