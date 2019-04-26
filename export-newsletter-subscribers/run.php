<?php 
/**
 * Export opted-in newsletter subscribers from DB.
 * This example simply renders a plain text output.
 */
require_once('classes/Database.php');


// ---------------------------------------------------------------------------------------------------------------------------------- Config

$storeId = 1;
$dbName  = '';
$dbUser  = '';
$dbPass  = '';

// null | Database instance
$emails = null;


// ----------------------------------------------------------------------------------------------------------------- Get subscribers from DB


try {
  $dbName = new Database();
  $emails = $dbName
    ->setDbAccess('localhost', $dbPass, $dbName, $dbUser)
    ->connect()
    ->query('
      SELECT
        store_id,
        subscriber_id,
        subscriber_email
      FROM
        `newsletter_subscriber`
      WHERE
        store_id=' . $storeId . ' AND
        subscriber_status=1
    ')
    ->toArray();
}
catch (Exception $e) {
  die;
}

if (!$emails) {
  header('Content-Type:text/plain, charset=utf-8');
  die('No emails found - Nothing exported.');
}


// --------------------------------------------------------------------------------------------------------------------- Prepare output data

$text = "store_id \tsubscriber_id \tsubscriber_email\n\n";

foreach ($emails as $email) {
  $text .=
    $email['store_id']     . "\t\t" .
    $email['subscriber_id']  . "\t\t" .
    $email['subscriber_email'] . "\n"
  ;
}


// Show as plain text
header('Content-Type:text/plain, charset=utf-8');
echo $text;


// DIY alternative: Save as CSV file
// header('Content-type:application/octet-stream');
// header('Content-Disposition:attachment; filename="newsletter_subscribers.csv"');
// ...

