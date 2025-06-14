<?php

namespace ManiExtensions;

interface IMySql
{
    public static function createIdentityConnection();
    public static function createResourceConnection();
    public static function sqlSelect($C, $query, $format = false, ...$args);
    public static function sqlInsert($C, $query, $format = false, ...$args);
    public static function sqlUpdate($C, $query, $format = false, ...$vars);
}

interface IDateExtension
{
    public static function formatDate($dateString);
}

interface IAuth
{
    public static function generateDefaultPassword($length);
    public static function createToken();
    public static function validateToken($token);
}

interface IEmailClient
{
    public static function sendMail($to, $subject, $body);
}

interface IValidator
{
    public static function validateUserInput($data);
    public static function validateLoginCredentials($C, $username, $password);
}

interface IActions
{
    public static function loadSpinner();
    public static function showAlert($title, $message, $icon, $redirectUrl = "", ...$params);
}

interface ISMSClient
{
    public function registerSenderId($senderId, $purpose);
    public function checkMessageStatus($messageId);
    public function sendSingleSms($number, $message);
    public function sendBulkSms($numbers, $message);
    public function sendMessageWithToken($number, $message);
    public function getSenderIds();
    public function balance();
}
