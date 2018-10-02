<?php

namespace Amirmasoud\Magfa;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Sms
{
    /**
     * @var string
     */
    private  $USERNAME;

    /**
     * @var string
     */
    private  $PASSWORD;

    /**
     * @var string
     */
    private  $DOMAIN;

    /**
     * @var string
     */
    private  $BASE_HTTP_URL = "http://messaging.magfa.com/magfaHttpService?";

    /**
     * @var int
     */
    private  $ERROR_MAX_VALUE = 1000;

    /**
     * @var array
     */
    private $errors = [
        1 => [
            "title" => "INVALID_RECIPIENT_NUMBER",
            "desc" => "the string you presented as recipient numbers are not valid phone numbers, please check them again"
        ],
        2 => [
            "title" => "INVALID_SENDER_NUMBER",
            "desc" => "the string you presented as sender numbers(3000-xxx) are not valid numbers, please check them again"
        ],
        3 => [
            "title" => "INVALID_ENCODING",
            "desc" => "are You sure You've entered the right encoding for this message? You can try other encodings to bypass this error code"
        ],
        4 => [
            "title" => "INVALID_MESSAGE_CLASS",
            "desc" => "entered MessageClass is not valid. for a normal MClass, leave this entry empty"
        ],
        6 => [
            "title" => "INVALID_UDH",
            "desc" => "entered UDH is invalid. in order to send a simple message, leave this entry empty"
        ],
        12 => [
            "title" => "INVALID_ACCOUNT_ID",
            "desc" => "you're trying to use a service from another account??? check your UN/Password/NumberRange again"
        ],
        13 => [
            "title" => "NULL_MESSAGE",
            "desc" => "check the text of your message. it seems to be null"
        ],
        14 => [
            "title" => "CREDIT_NOT_ENOUGH",
            "desc" => "Your credit's not enough to send this message. you might want to buy some credit.call"
        ],
        15 => [
            "title" => "SERVER_ERROR",
            "desc" => "something bad happened on server side, you might want to call MAGFA Support about this:"
        ],
        16 => [
            "title" => "ACCOUNT_INACTIVE",
            "desc" => "Your account is not active right now, call -- to activate it"
        ],
        17 => [
            "title" => "ACCOUNT_EXPIRED",
            "desc" => "looks like Your account's reached its expiration time, call -- for more information"
        ],
        18 => [
            "title" => "INVALID_USERNAME_PASSWORD_DOMAIN",
            "desc" => "the combination of entered Username/Password/Domain is not valid. check'em again"
        ],
        19 => [
            "title" => "AUTHENTICATION_FAILED",
            "desc" => "You're not entering the correct combination of Username/Password"
        ],
        20 => [
            "title" => "SERVICE_TYPE_NOT_FOUND",
            "desc" => "check the service type you're requesting. we don't get what service you want to use. your sender number might be wrong, too."
        ],
        22 => [
            "title" => "ACCOUNT_SERVICE_NOT_FOUND",
            "desc" => "your current number range doesn't have the permission to use Webservices"
        ],
        23 => [
            "title" => "SERVER_BUSY",
            "desc" => "Sorry, Server's under heavy traffic pressure, try testing another time please"
        ],
        24 => [
            "title" => "INVALID_MESSAGE_ID",
            "desc" => "entered message-id seems to be invalid, are you sure You entered the right thing?"
        ],
        102 => [
            "title" => "WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_MESSAGE_CLASS_ARRAY",
            "desc" => "this happens when you try to define MClasses for your messages. in this case you must define one recipient number for each MClass"
        ],
        103 => [
            "title" => "WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_SENDER_NUMBER_ARRAY",
            "desc" => "This error happens when you have more than one sender-number for message. when you have more than one sender number, for each sender-number you must define a re ▶"
        ],
        104 => [
            "title" => "WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_MESSAGE_ARRAY",
            "desc" => "this happens when you try to define UDHs for your messages. in this case you must define one recipient number for each udh"
        ],
        106 => [
            "title" => "WEB_RECIPIENT_NUMBER_ARRAY_IS_NULL",
            "desc" => "array of recipient numbers must have at least one member"
        ],
        107 => [
            "title" => "WEB_RECIPIENT_NUMBER_ARRAY_TOO_LONG",
            "desc" => "the maximum number of recipients per message is 90"
        ],
        108 => [
            "title" => "WEB_SENDER_NUMBER_ARRAY_IS_NULL",
            "desc" => "array of sender numbers must have at least one member"
        ],
        109 => [
            "title" => "WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_ENCODING_ARRAY",
            "desc" => "this happens when you try to define encodings for your messages. in this case you must define one recipient number for each Encoding"
        ],
        110 => [
            "title" => "WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_CHECKING_MESSAGE_IDS__ARRAY",
            "desc" => "this happens when you try to define checking-message-ids for your messages. in this case you must define one recipient number for each checking-message-id"
        ],
        -1 => [
            "title" => "NOT_AVAILABLE",
            "desc" => "The target of report is not available(e.g. no message is associated with entered IDs)"
        ]
    ];

    /**
     * Sms constructor.
     */
    public function __construct() {
        $this->USERNAME = config('magfa.username');
        $this->PASSWORD = config('magfa.password');
        $this->DOMAIN   = config('magfa.domain');

        $this->PASSWORD = urlencode($this->PASSWORD);
    }

    /**
     * Send SMS message.
     *
     * @param  string $recipientNumber
     * @param  string $message the content of the message
     * @param  string $udh udh of the message
     * @param  string $coding coding of the message
     * @param  string $checkingMessageId checking message id
     * @return \Exception|string
     */
    public function send(string $recipientNumber, string $message, string $udh = '', string $coding = '', string $checkingMessageId = '') {
        $client = new Client(); //GuzzleHttp\Client
        $result = $client->get($this->BASE_HTTP_URL .
            "service=enqueue" .
            "&username=" . $this->USERNAME . "&password=" . $this->PASSWORD . "&domain=" . $this->DOMAIN .
            "&from=" . config('magfa.sender') . "&to=" . $recipientNumber .
            "&message=" . urlencode($message) . "&coding=" . $coding . "&udh=" . $udh .
            "&chkmessageid=" . $checkingMessageId);

        $result = $result->getBody()->getContents();

        // compare the response with the ERROR_MAX_VALUE
        if ($result <= $this->ERROR_MAX_VALUE) {
            throw new \Exception($this->errors[$result]['title'] . ': ' . $this->errors[$result]['desc'], $result);
        } else {
            return $result;
        }
    }
}
