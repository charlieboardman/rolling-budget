<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rolling Budget App</title>

    <style>
        table {
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
        }
    </style>

</head>

<!--Check when app was last opened and add number of days needed-->
<?php

#Functions
function transact($date,$amount,$vendor,$description) {
    
    $old_total = floatval(file_get_contents('total.txt'));

    $new_total = $old_total + $amount;

    file_put_contents('total.txt',$new_total);

    $date_str = $date->format('Y-m-d');

    $new_transaction = "$date_str,$amount,$vendor,$description,$new_total\n";

    return $new_transaction;
}

#Begin main logic

#Set the timezone
date_default_timezone_set('America/Los_Angeles');

#Get today's date
$today = new DateTime(date("Y-m-d"));

#Get date the app was last opened
$last_opened = new DateTime(file_get_contents('date-last-opened.txt'));

#Time since last opening
$days_since_opening = date_diff($last_opened,$today,TRUE)->format('%a');

if ($days_since_opening != 0) {
    #Get daily increment
    $daily_increment = file_get_contents('daily-increment.txt');
    $new_line = transact($today,$daily_increment*$days_since_opening, "", "Daily increment ($days_since_opening)");
    file_put_contents('transactions.csv', $new_line, FILE_APPEND);
}

#Reset the last opened date
file_put_contents('date-last-opened.txt',$today->format('Y-m-d'));

//These are for debugging
echo $today->format('Y-m-d');
echo "<br>";
echo $last_opened->format('Y-m-d');
echo "<br>";
echo $days_since_opening;
echo "<br>";
echo $daily_increment;
echo "<br>";
echo $new_line;
?>

<table>
    <tr>
        <th>Date</th>
        <th>Amount</th>
        <th>Vendor</th>
        <th>Description</th>
        <th>New Total</th>
    </tr>

    <?php

    #Read the last n lines of the transaction file. If there are less than n, read however many there are
    
    #n last lines you want
    $n = 5;

    $lines = file('transactions.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $nth_last_lines = array_slice($lines, -$n);

    foreach ($nth_last_lines as $line) {
        $fields = str_getcsv($line);

        $date = $fields[0];
        $amount_raw = $fields[1];
        
        if ($amount_raw < 0){
            $amount = "-$" . number_format(abs($amount_raw));
        } else {
            $amount = "$" . number_format($amount_raw);
        }
        
        $vendor = $fields[2];
        $description = $fields[3];
        $new_total_raw = $fields[4];

        if ($new_total_raw < 0){
            $new_total = "-$" . number_format(abs($new_total_raw));
        } else {
            $new_total = "$" . number_format($new_total_raw);
        }
        echo "<tr>
                <td>$date</td>
                <td>$amount</td>
                <td>$vendor</td>
                <td>$description</td>
                <td>$new_total</td>
            </tr>";
    }
    ?>

</table>
</html>