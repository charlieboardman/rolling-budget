<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rolling Budget App</title>
</head>

<!--Check when app was last opened and add number of days needed-->
<?php

#Functions
function transact($date,$amount,$type,$vendor,$description) {
    
    $old_total = floatval(file_get_contents('total.txt'));

    if ($type == 'gain') {
        $new_total = $old_total + $amount;
    }
    
    if ($type == 'spend') {
        $new_total = $old_total - $amount;
    }

    $date_str = $date->format('Y-m-d');

    $new_transaction = "$date_str,$amount,$type,$vendor,$description,$new_total\n";

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
    $days_since_opening_int = $days_since_opening;
    $new_line = transact($today,$daily_increment * $days_since_opening_int,'gain','',"Daily increment ($days_since_opening_int)");
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
        <th>Type</th>
        <th>Vendor</th>
        <th>Description</th>
    </tr>

    <?php

    #Read the last n lines of the transaction file. If there are less than 5, read however many there are
    
    #n last lines you want
    $n = 5;

    $lines = file('transactions.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $nth_last_lines = array_slice($lines, -$n);

    foreach ($nth_last_lines as $line) {
        $fields = str_getcsv($line);

        $date = $fields[0];
        $amount = $fields[1];
        $type = $fields[2];
        $vendor = $fields[3];
        $description = $fields[4];

        echo "<tr>
                <td>$date</td>
                <td>$amount</td>
                <td>$type</td>
                <td>$vendor</td>
                <td>$description</td>
            </tr>";
    }
    ?>

</table>
</html>