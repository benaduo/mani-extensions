<?php

/********************************** Giant SMS ***********************************
 * *******************************************************************************
 * * Copyright (c) FingerGiant Ltd 2018. All rights reserved                    **
 * * Version       1.0                                                          **
 * * Email         manuelxtrem@fingergiant.com                                  **
 * * Web           http://giantsms.com/                                         **
 * * Web           http://fingergiant.com/                                      **
 * *******************************************************************************
 * ******************************************************************************/

namespace BulkSMS\Model;

class SMSResponse {
    public $status;
    public $message;

    function __construct($arrOrObj) {
        if(is_array($arrOrObj)) {
            $this->status = isset($arrOrObj['status']) ? filter_var($arrOrObj['status'], FILTER_VALIDATE_BOOLEAN) : false;
            $this->message = isset($arrOrObj['message']) ? $arrOrObj['message'] : 'A fatal error occurred';
        } else {
            $this->status = isset($arrOrObj->status) ? filter_var($arrOrObj->status, FILTER_VALIDATE_BOOLEAN) : false;
            $this->message = isset($arrOrObj->message) ? $arrOrObj->message : 'A fatal error occurred';
        }
    }
}