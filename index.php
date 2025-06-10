<?php
require_once 'mani-extensions.php';

use ManiExtensions\SMSClient;


$smsClient = new SMSClient(SMS_API_USERNAME, SMS_API_PASSWORD);

// Get SMS Balance
$balance = $smsClient->balance();
if ($balance->getStatus()) {
    // echo "Balance: " . $balance->getMessage();
} else {
    echo "Error: " . $balance->getMessage();
}

// Get Sender IDs
$senderIds = $smsClient->getSenderIds();
if ($senderIds->getStatus()) {

    echo "Sender IDs: " . json_encode($senderIds->toApiResponse(), JSON_PRETTY_PRINT);

    // print the objects in a more readable format
    /*  foreach ($senderIds->toArray()['data'] as $id) {
        echo " Name - " . $id['name'] . "<br/>";
        echo " Purpose - " . $id['purpose'] . "<br/>";
        echo " Approved - " . ($id['approved'] ? 'Yes' : 'No') . "<br/>";
        echo " Approval Status - " . $id['approval_status'] . "<br/>";
    } */
} else {
    echo "Error: " . $senderIds->getMessage();
}


// Send Single SMS

$singleSmsResponse = $smsClient->sendSingleSms('0200000000', 'Hello, this is a test message.');
if ($singleSmsResponse->getStatus()) {
    echo "Single SMS sent successfully. Message ID: " . $singleSmsResponse->getMessageStatus()->getMessageId();
} else {
    echo "Error sending single SMS: " . $singleSmsResponse->getMessage();
}

echo "<br/>";
// DB9FFFB0-EDAD-4F78-BE99-58F47539A000

// Check Message Status
$messageStatus = $smsClient->checkMessageStatus('DB9FFFB0-EDAD-4F78-BE99-58F47539A000');
if ($messageStatus->getStatus()) {
    echo "Message Status: " . json_encode($messageStatus->toApiResponse(), JSON_PRETTY_PRINT);
} else {
    echo "Error checking message status: " . $messageStatus->getMessage();
}

echo "<br/>";

// Send Bulk SMS
$numbers = ['0200000000', '0540000000'];
$bulkSmsResponse = $smsClient->sendBulkSms($numbers, 'Hello, this is a test bulk message.');
if ($bulkSmsResponse->getStatus()) {
    echo  json_encode($bulkSmsResponse->toApiResponse(), JSON_PRETTY_PRINT);
} else {
    echo "Error sending bulk SMS: " . $bulkSmsResponse->getMessage();
}

