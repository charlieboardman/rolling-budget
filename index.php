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
$days_since_opening = date_diff($last_opened,$today,TRUE);

if ($days_since_opening != 0) {
    #Get daily increment
    $daily_increment = file_get_contents('daily-increment.txt');
    $days_since_opening_int = $days_since_opening->format('%a');
    $new_line = transact($today,$daily_increment * $days_since_opening_int,'gain','',"Daily increment ($days_since_opening_int)");
    file_put_contents('transactions.csv',$new_line, FILE_APPEND);
}



echo $today->format('Y-m-d');
echo "<br>";
echo $last_opened->format('Y-m-d');
echo "<br>";
echo $days_since_opening->format('%a');
echo "<br>";
echo $daily_increment;
echo "<br>";
echo $new_line;
?>
