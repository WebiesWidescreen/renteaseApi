<?php
class LoginComponent
{
    public $LoginCheckToken;
    public $SignUpOTPCheckToken;
    public $ResendOTPCheckToken;
    public $SignUpAVNCheckToken;

    public function loginCheck()
    {
        include('config.inc');
        include('readConfig.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';
        try {

            //Decode Token Start
            $secratekey = "USERCHECK";
            $decodeVal = decodeToken($this->LoginCheckToken['UserToken'], $secratekey);
            //Decode Token End

            $userCheck = 0;
            $loginCheck = 0;
            $rsdInsert = false;
            $rsd = false;
            $rsdPIN = false;
            $lastInsertUserID = '';
            $userName = '';
            $phoneNumber = '';


            if (isset($decodeVal->userName)) {
                $queryUserList = "SELECT tblU.userID, tblU.userName, tblU.userPhone FROM tbl_user tblU WHERE tblU.userPhone = '$decodeVal->userPhone' AND tblU.isActive = 1";
                $rsdUserList = mysqli_query($connect_read_var, $queryUserList);
                while ($rsUserList = mysqli_fetch_assoc($rsdUserList)) {
                    $userCheck++;
                }
                if ($userCheck == 0) {
                    $queryCreateUser = "INSERT INTO tbl_user(userName, userPhone, createdOn) VALUES ('$decodeVal->userName', '$decodeVal->userPhone', NOW())";
                    $rsdInsert = mysqli_query($connect_var, $queryCreateUser);
                    $lastInsertUserID =  mysqli_insert_id($connect_var);
                    $userName = $decodeVal->userName;
                    $phoneNumber = $decodeVal->userPhone;
                }
            } else {
                $queryUserList = "SELECT tblU.userID, tblU.userName, tblU.userPhone FROM tbl_user tblU WHERE tblU.userPhone = '$decodeVal->userPhone' AND tblU.userPassword = '$decodeVal->userAVN' AND tblU.isActive = 1";
                $rsd = mysqli_query($connect_read_var, $queryUserList);
                while ($rsUserList  = mysqli_fetch_assoc($rsd)) {
                    $lastInsertUserID = $rsUserList['userID'];
                    $userName = $rsUserList['userName'];
                    $phoneNumber = $rsUserList['userPhone'];
                    $loginCheck++;
                }
            }

            if ($rsdInsert && $userCheck == 0) {
                $six_digit_random_number = mt_rand(100000, 999999);

                // $TemplateID = '1207168396384720245';
                // sendMSG("Your OTP is $six_digit_random_number.", $phoneNumber, $TemplateID);

                $queryOtpInsert = "INSERT INTO tbl_pin (code, userID, createdOn) VALUES ('$six_digit_random_number ', '$lastInsertUserID', NOW())";
                $rsdPIN = mysqli_query($connect_var, $queryOtpInsert);
            }


            if ($rsdPIN || ($rsd && $loginCheck > 0)) {
                $responseCode = "01";
                $responseMessage = 'Success';
            } else if ($userCheck > 0) {
                $responseCode = "02";
                $responseMessage = 'User phone number is already exist';
            }

            //Encode Token Start
            $payload_info = array(
                "message" => $responseMessage
            );
            $encodeToken = encodeToken($payload_info, $secratekey);
            //Encode Token End

            mysqli_close($connect_read_var); //Read Connection Close
            mysqli_close($connect_var); //Write Connection Close

            echo json_encode(array("statusCode" => $responseCode, "response" => $encodeToken, "userID" => $lastInsertUserID, "userName" => $userName, "phoneNumber" => $phoneNumber)); //Return Response

        } catch (PDOException $e) {
            echo json_encode(array("status" => "errors", "message_text" => $e->getMessage()), JSON_FORCE_OBJECT);
        }
    }

    public function signUpOTPCheck()
    {
        include('config.inc');
        include('readConfig.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';
        try {

            //Decode Token Start
            $secratekey = "SIGNUPOTP";
            $decodeVal = decodeToken($this->SignUpOTPCheckToken['SignUpOTPToken'], $secratekey);
            //Decode Token End

            $rsdDelete = false;
            $queryUserPin = "SELECT pinID, code FROM tbl_pin WHERE userID = '$decodeVal->userID' ORDER BY userID DESC";
            $rsd = mysqli_query($connect_read_var, $queryUserPin);
            while ($rs = mysqli_fetch_assoc($rsd)) {
                if ($rs['code'] == $decodeVal->userOTP) {
                    $quertDelete = "DELETE FROM tbl_pin WHERE pinID = '" . $rs['pinID'] . "' AND userID = '$decodeVal->userID'";
                    $rsdDelete = mysqli_query($connect_var, $quertDelete);
                }
            }

            if ($rsdDelete) {
                $responseCode = "01";
                $responseMessage = 'Success';
            }

            //Encode Token Start
            $payload_info = array(
                "message" => $responseMessage
            );
            $encodeToken = encodeToken($payload_info, $secratekey);
            //Encode Token End

            mysqli_close($connect_read_var); //Read Connection Close
            mysqli_close($connect_var); //Write Connection Close

            echo json_encode(array("statusCode" => $responseCode, "response" => $encodeToken)); //Return Response

        } catch (PDOException $e) {
            echo json_encode(array("status" => "errors", "message_text" => $e->getMessage()), JSON_FORCE_OBJECT);
        }
    }

    public function resendOTPCheck()
    {
        include('config.inc');
        include('readConfig.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';
        try {

            //Decode Token Start
            $secratekey = "RESENDOTP";
            $decodeVal = decodeToken($this->ResendOTPCheckToken['ResendOTPToken'], $secratekey);
            //Decode Token End
            $rsdDelete = false;
            $rsdPIN = false;

            $queryUserList = "SELECT tblU.userID, tblU.userName, tblU.userPhone FROM tbl_user tblU WHERE tblU.userPhone = '$decodeVal->userPhone' AND tblU.isActive = 1";
            $rsdUserList = mysqli_query($connect_read_var, $queryUserList);
            while ($rsUserList = mysqli_fetch_assoc($rsdUserList)) {
                $six_digit_random_number = mt_rand(100000, 999999);

                // $TemplateID = '1207168396384720245';
                // sendMSG("Your OTP is $six_digit_random_number", $decodeVal->userPhone, $TemplateID);

                $quertDelete = "DELETE FROM tbl_pin WHERE userID = '" . $rsUserList['userID'] . "'";
                $rsdDelete = mysqli_query($connect_var, $quertDelete);

                $queryPinNumberInsert = "INSERT INTO tbl_pin (code, userID, createdOn) VALUES ('$six_digit_random_number ', '" . $rsUserList['userID'] . "', NOW())";
                $rsdPIN = mysqli_query($connect_var, $queryPinNumberInsert);
            }

            if ($rsdDelete && $rsdPIN) {
                $responseCode = "01";
                $responseMessage = 'Success';
            }

            //Encode Token Start
            $payload_info = array(
                "message" => $responseMessage
            );
            $encodeToken = encodeToken($payload_info, $secratekey);
            //Encode Token End

            mysqli_close($connect_read_var); //Read Connection Close
            mysqli_close($connect_var); //Write Connection Close

            echo json_encode(array("statusCode" => $responseCode, "response" => $encodeToken)); //Return Response

        } catch (PDOException $e) {
            echo json_encode(array("status" => "errors", "message_text" => $e->getMessage()), JSON_FORCE_OBJECT);
        }
    }

    public function signUpAVNCheck()
    {
        include('config.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';
        try {

            //Decode Token Start
            $secratekey = "SIGNUPAVN";
            $decodeVal = decodeToken($this->SignUpAVNCheckToken['SignUpAVNToken'], $secratekey);
            //Decode Token End

            $queryUpdateUserAVN = "UPDATE tbl_user SET userPassword='$decodeVal->userConfirmAVN', modifiedOn=NOW() WHERE userID = '$decodeVal->userID'";
            $rsd = mysqli_query($connect_var, $queryUpdateUserAVN);

            if ($rsd) {
                $responseCode = "01";
                $responseMessage = 'Success';
            }

            //Encode Token Start
            $payload_info = array(
                "message" => $responseMessage
            );
            $encodeToken = encodeToken($payload_info, $secratekey);
            //Encode Token End

            mysqli_close($connect_var); //Write Connection Close

            echo json_encode(array("statusCode" => $responseCode, "response" => $encodeToken)); //Return Response

        } catch (PDOException $e) {
            echo json_encode(array("status" => "errors", "message_text" => $e->getMessage()), JSON_FORCE_OBJECT);
        }
    }
} // Class End Here


function checkLogin(array $data)
{
    $LoginObject = new LoginComponent;
    if ($data) {
        $LoginObject->LoginCheckToken = $data;
        $LoginObject->loginCheck();
    } else
        echo json_encode(array("status" => "error On Login", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}

function checkSignUpOTP(array $data)
{
    $LoginObject = new LoginComponent;
    if ($data) {
        $LoginObject->SignUpOTPCheckToken = $data;
        $LoginObject->signUpOTPCheck();
    } else
        echo json_encode(array("status" => "error On Check AVN SignUp", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}

function checkResendOTP(array $data)
{
    $LoginObject = new LoginComponent;
    if ($data) {
        $LoginObject->ResendOTPCheckToken = $data;
        $LoginObject->resendOTPCheck();
    } else
        echo json_encode(array("status" => "error On Check AVN SignUp", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}

function checkAVNSignUp(array $data)
{
    $LoginObject = new LoginComponent;
    if ($data) {
        $LoginObject->SignUpAVNCheckToken = $data;
        $LoginObject->signUpAVNCheck();
    } else
        echo json_encode(array("status" => "error On Check AVN SignUp", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}
