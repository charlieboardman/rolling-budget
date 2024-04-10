<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VMServer</title>
</head>

<!--Check when app was last opened and add number of days needed-->
<?php

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
    $daily_increment = file_get_contents('daily-increment.txt'); #Need to write this function
    increment($days_since_opening,$daily_increment);
}


echo $today->format('Y-m-d');
echo "<br>";
echo $last_opened->format('Y-m-d');
echo "<br>";
echo $days_since_opening->format('%a');
echo "<br>";
echo $daily_increment;
?>
