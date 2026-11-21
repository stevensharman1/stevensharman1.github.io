
<?php
// Set the event date.
$target = mktime(0, 0, 0, 12, 10, 2018);

// Get today's date.
$today = time();

// Calculate the difference in seconds.
$difference = $target - $today;

// Convert the difference into days.
$days = (int) ceil(abs($difference) / 86400);

// Display the result.
if ($difference > 0) {
    print "Our event will occur in $days days.";
} elseif ($difference < 0) {
    print "Our event occurred $days days ago.";
} else {
    print "Our event is today!";
}
?>
