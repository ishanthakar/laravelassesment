<?php
namespace App\Traits;
use DB;
use Auth;
use Validator;
use Carbon\Carbon;

trait CommonTrait {

    
    public static function getOtpForUser($length = 6)
    {
        $otpGeneratorString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        return $otpGeneratorString;
    }

}
