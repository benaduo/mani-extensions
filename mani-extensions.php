<?php

/**
 * Author: Benjamin Aduo
 * Purpose: This file contains classes and interfaces that can be used to extend the functionality of your application.
 */

namespace ManiExtensions;

require_once 'app-config.php';
require 'vendor/autoload.php';
include 'interfaces.php';

use DateTime;
use mysqli;

use Mailgun\Mailgun;

use BulkSMS\GiantSMS;



class DateExtension implements IDateExtension
{
    /**
     * Returns a date in this format (eg. 1st January, 1970).
     *
     * @param string   $dateString         string format of date
     *
     * @return DateTime|string
     */
    public static function formatDate($dateString)
    {
        if ($dateString === null) {
            return null;
        } else {
            $date = new DateTime($dateString);
            $formattedDate = $date->format('jS F, Y');
            return $formattedDate;
        }
    }
}

class MySql implements IMySql
{
    /**

     * Connects to the Identity database using the configuration details defined in the constants.
     * @return mysqli|false Returns a mysqli object if connection is successful, or false on failure.
     */
    public static function createIdentityConnection()
    {
        $C = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, IDENTITY_DATABASE);

        if ($C->connect_error) {
            return false;
        }
        return $C;
    }


    /**

     * Connects to the Resource database using the configuration details defined in the constants.
     * @return mysqli|false Returns a mysqli object if connection is successful, or false on failure.
     */
    public static function createResourceConnection()
    {
        $C = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, RESOURCE_DATABASE);
        if ($C->connect_error) {
            return false;
        }
        return $C;
    }


    /**
     * Executes a SQL query and returns the result set.
     * @param mysqli $C The mysqli object to use for database connection
     * @param string $query The SQL query to execute
     * @param string $format Optional. The format string for parameters to be bound in the query (i - integer, s - string, b - byte)
     * @param mixed $args Optional. Values to bind to the query parameters
     * @return mysqli_result|false Returns the mysqli_result object if the query was executed successfully. Returns false if there was an error.
     */
    public static function sqlSelect($C, $query, $format = false, ...$args)
    {
        $stmt = $C->prepare($query);
        if ($stmt === false) {

            echo mysqli_error($C);
            return false; // Add error handling if prepare fails
        }

        if ($format) {
            $stmt->bind_param($format, ...$args);
        }
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }
        $stmt->close();
        return false;
    }


    /**
     * Inserts data into the database using a prepared statement and returns the ID of the inserted row.
     * @param mysqli $C The mysqli object representing the database connection
     * @param string $query The query to be executed
     * @param bool|string $format Optional. The format string representing the types of data to be inserted (i - integer, s - string, b - byte)
     * @param mixed ...$args Optional. The data to be inserted
     * @return int The ID of the inserted row, or -1 on failure
     */
    public static function sqlInsert($C, $query, $format = false, ...$args)
    {
        $stmt = $C->prepare($query);
        if ($format) {
            $stmt->bind_param($format, ...$args);
        }
        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            $stmt->close();
            return $id;
        }
        $stmt->close();
        return -1;
    }


    /**
     * Executes an SQL update or delete query on the provided database connection object.
     * @param mysqli $C The database connection object to execute the query on.
     * @param string $query The SQL query to execute.
     * @param mixed $format (Optional) The format string for the prepared statement. (i - integer, s - string, b - byte)
     * @param mixed $vars (Optional) The variables to bind to the prepared statement. Default is an empty array.@return bool Returns true if the query was executed successfully, false otherwise.
     * 
     */
    public static function sqlUpdate($C, $query, $format = false, ...$vars)
    {
        $stmt = $C->prepare($query);
        if ($format) {
            $stmt->bind_param($format, ...$vars);
        }
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $error_message = $stmt->error;
            $stmt->close();
            return $error_message;
        }
    }
}

class Auth implements IAuth
{
    /**
     * Generates a random string of given length for use as default password
     * @param int $length The length of the password to be generated
     * @return string The generated password
     */
    public static function generateDefaultPassword($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/*-/\\";
        $password = substr(str_shuffle($chars), 0, $length);
        return $password;
    }


    /**
     * Creates a secure token by encoding random bytes and using a hash_hmac function.
     * @return string The secure token.
     */
    public static function createToken()
    {
        $seed = self::urlSafeEncode(random_bytes(8));
        $t = time();
        $hash = self::urlSafeEncode(hash_hmac('sha256', session_id() . $seed . $t, @CSRF_TOKEN_SECRET, true));
        return self::urlSafeEncode($hash . '|' . $seed . '|' . $t);
    }


    /**
     * Validates the provided token by checking if the hash_hmac function returns a match.
     * @param string $token The token to validate.
     * @return bool True if the token is valid, false otherwise.
     */
    public static function validateToken($token)
    {
        $parts = explode('|', self::urlSafeDecode($token));
        if (count($parts) === 3) {
            $hash = hash_hmac('sha256', session_id() . $parts[1] . $parts[2], CSRF_TOKEN_SECRET, true);
            if (hash_equals($hash, self::urlSafeDecode($parts[0]))) {
                return true;
            }
        }
        return false;
    }

    private static function urlSafeEncode($m)
    {
        return rtrim(strtr(base64_encode($m), '+/', '-_'), '=');
    }

    private static function urlSafeDecode($m)
    {
        return base64_decode(strtr($m, '-_', '+/'));
    }
}

class EmailClient implements IEmailClient
{
    /**
     * Send an email using Mailgun API
     * @param string $to The recipient's email address
     * @param string $subject The subject of the email
     * @param string $body The body of the email (in html)
     * @return SendResponse|ResponseInterface
     */
    public static function sendMail($to, $subject, $body)
    {
        try {
            $mgClient = Mailgun::create(API_KEY, API_HOSTNAME);
            $domain = APP_DOMAIN;
            $params = array(
                'from' => FROM,
                'to' => $to,
                'subject' => $subject,
                'html' => $body
            );

            $response = $mgClient->messages()->send($domain, $params);
            return $response;
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
}
class Validator implements IValidator
{
    /**
     * Validates user input to prevent SQL injection attacks
     * @param string $data The user input to be validated
     * 
     */
    public static function validateUserInput($data)
    {
        $C = MySql::createResourceConnection();
        trim($data);
        strip_tags($data);
        stripslashes($data);
        htmlspecialchars($data);
        mysqli_real_escape_string($C, $data);
        return $data;
    }
    /**
     * Validates the user's login credentials
     * @param mysqli $C The mysqli object representing the database connection
     * @param string $username The username to be validated
     * @param string $password The password to be validated
     */
    public static function validateLoginCredentials($C, $username, $password)
    {
        if ($C) {
            $res = MySql::sqlSelect($C, 'SELECT user.id, user.username, user.password, role.name FROM user
            INNER JOIN role ON user.role_id = role.id
            WHERE username = ?', 's', $username);

            if ($res && $res->num_rows === 1) {
                $user = $res->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['USER_ID'] = $user['id'];
                    $_SESSION['ROLE'] = $user['name'];
                    $_SESSION['USERNAME'] = $user['username'];
                    header('Location: dashboard.php'); // redirect to dashboard, specify the path to the dashboard
                    $res->free_result();
                } else {
                    echo mysqli_error($C);
                    $_SESSION['status'] = "Invalid login credentials";
                    $_SESSION['status_code'] = "error";
                }
            } else {
                $_SESSION['status'] = "Invalid login credentials";
                $_SESSION['status_code'] = "error";
            }
        }
    }
}


class Actions implements IActions
{

    /**
     * Invokes the loadSpinner() function and returns the HTML content
     */
    public static function loadSpinner()
    {
        ob_start();
        include '../mani-extensions/assets/html/spinner.html';
        $content = ob_get_clean();
        return $content;
    }

    /**
     * Invokes the SweetAlert2 library and returns the HTML content
     * @param string $title The title of the alert
     * @param string $message The message to be displayed
     * @param string $icon The icon to be displayed (success, error, warning, info, question)
     * @param string $redirectUrl The URL to redirect to after the alert is closed
     * @param string $params Optional. Additional parameters to be passed to the SweetAlert2 library
     */
    public static function showAlert($title, $message, $icon, $redirectUrl = "", ...$params)
    {
        $params = implode(',', $params);
        $html = "<script src=\"../mani-extensions/assets/sweetalert/sweetalert2.all.min.js\"></script>";
        $html .= "<script>Swal.fire(
            {
                title: '$title',
                text: '$message',
                icon: '$icon',
                confirmButtonText: 'OK',
                $params
            }
        ).then(() => {
            window.location = '$redirectUrl';
        });</script>";
        return $html;
    }
    /**
     * Invokes a SweetAlert2 confirmation dialog and returns the HTML content
     * @param string $title The title of the alert
     * @param string $message The message to be displayed
     * @param string $confirmCallback The callback function to be executed if the user confirms the action
     */
    public static function confirmAction($title, $message, $confirmCallback)
    {
        $html = "<script src=\"../mani-extensions/assets/sweetalert/sweetalert2.all.min.js\"></script>";
        $html .= "<script>Swal.fire({
            title: '$title',
            text: '$message',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, do it!'
        }).then((result) => {
            if (result.isConfirmed) {
                " . $confirmCallback . "
            }
        });</script>";
        return $html;
    }
}

class SMSClient implements ISMSClient
{

    /**
     * Sends a bulk SMS to multiple recipient
     * @param string $numbersArray An array of recipients' phone number
     * @param string $message The message to be sent
     */

    public static function sendBulkSms($numbersArray, $message)
    {
        $curl = curl_init();

        $data = [
            'from' => SMS_SENDER_ID,
            'recipients' => $numbersArray,
            'msg' => $message
        ];

        $jsonData = json_encode($data);

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.giantsms.com/api/v1/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => [
                "Accept: */*",
                "Authorization: Basic " . SMS_TOKEN . "",
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            $cleanedResponse = trim($response, " \t\n\r\0\x0BNULL");
            return $cleanedResponse;
        }
    }

    /**
     * Checks the SMS balance
     */
    public static function checkSMSBalance()
    {
        $sms = new GiantSMS(SMS_API_USERNAME, SMS_API_SECRET);

        var_dump($sms->balance());
    }
}

class PostgreSql
{
    public static  function createPostgreConnection()
    {
        $host = "host = " . PG_DB_HOST . "";
        $port = "port = " . PG_DB_PORT . "";
        $dbname = "dbname = " . PG_DB_NAME . "";
        $credentials = "user = " . PG_DB_USERNAME . " password = " . PG_DB_PASSWORD . "";

        $db = pg_connect("$host $port $dbname $credentials");

        if (!$db) {
            return false;
        }

        return $db;
    }
}
