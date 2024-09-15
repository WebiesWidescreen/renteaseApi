<?php
class LoginComponent
{
    public $LoginUserToken;

    public function userLogin()
    {
        include('config.inc');
        include('readConfig.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';
        $resultArr = array();
        try {

            //Decode Token Start
            $secratekey = "UserLoginKey";
            $decodeVal = decodeToken($this->LoginUserToken['UserLoginToken'], $secratekey);
            //Decode Token End

            // PRODUCT TRANSACTION STATUS CHECK
            $queryUserList = "SELECT tblU.userID, tblU.userName, tblU.phoneNumber, tblU.mailID FROM tblUser tblU WHERE tblU.phoneNumber = '$decodeVal->PhoneNumber' AND tblU.isActive = 1";
            $rsdUserList = mysqli_query($connect_read_var, $queryUserList);
            $userExist = 0;
            while ($rsUserList = mysqli_fetch_assoc($rsdUserList)) {
                $userID = $rsUserList['userID'];
                $userName = $rsUserList['userName'];
                $phoneNumber = $rsUserList['phoneNumber'];
                $mailID = $rsUserList['mailID'];
                $userExist++;
            }

            if ($userExist == 0) {
                $queryCreateUser = "INSERT INTO tblUser(userName, phoneNumber, mailID) VALUES ('$decodeVal->UserName', '$decodeVal->PhoneNumber', '$decodeVal->Email')";
                $rsd = mysqli_query($connect_var, $queryCreateUser);
                $lastInsertUserID =  mysqli_insert_id($connect_var);

                $userID = $lastInsertUserID;
                $userName = $decodeVal->UserName;
                $phoneNumber = $decodeVal->PhoneNumber;
                $mailID = $decodeVal->Email;
            }

            if ($rsd && $userExist == 0) {
                $responseCode = "01";
                $responseMessage = 'Success';
            } else if ($userExist > 0) {
                $responseCode = "02";
                $responseMessage = 'Already Exist User';
            }

            //Encode Token Start
            $payload_info = array(
                "data" => $resultArr,
                "message" => $responseMessage
            );
            $encodeToken = encodeToken($payload_info, $secratekey);
            //Encode Token End

            mysqli_close($connect_read_var); //Read Connection Close
            mysqli_close($connect_var); //Write Connection Close

            echo json_encode(array("statusCode" => $responseCode, "response" => $encodeToken, "UserID" => $userID, "UserName" => $userName, "PhoneNumber" => $phoneNumber, "MailID" => $mailID)); //Return Response

        } catch (PDOException $e) {
            echo json_encode(array("status" => "errors", "message_text" => $e->getMessage()), JSON_FORCE_OBJECT);
        }
    }
} // Class End Here


function loginUser(array $data)
{
    $LoginObject = new LoginComponent;
    if ($data) {
        $LoginObject->LoginUserToken = $data;
        $LoginObject->userLogin();
    } else
        echo json_encode(array("status" => "error On Login Details", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}
