<?php
class DashboardComponent
{
    public $GetBroadbandListToken;

    public function getBroadbandList()
    {
        include('config.inc');
        include('readConfig.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';
        $count = 0;
        $resultArr = array();
        try {

            //Decode Token Start
            $secratekey = "GetBroadbandListKey";
            $decodeVal = decodeToken($this->GetBroadbandListToken['GetBroadbandListData'], $secratekey);
            //Decode Token End

            $inputSearchFilter = "";
            $SortBy = "";

            if ($decodeVal->SearchValueSet != "") {
                $inputSearchFilter = "AND (tblB.name LIKE '%{$decodeVal->SearchValueSet}%' OR tblB.lowest_price LIKE '%{$decodeVal->SearchValueSet}%' OR tblB.rating LIKE '%{$decodeVal->SearchValueSet}%')";
            }

            if ($decodeVal->SortBy == "Price") {
                $SortBy = "ORDER BY tblB.lowest_price ASC";
            }

            if ($decodeVal->SortBy == "Rating") {
                $SortBy = "ORDER BY tblB.rating DESC";
            }

            $queryBroadbandList = "SELECT * FROM tblBroadband tblB WHERE tblB.isActive = 1 $inputSearchFilter $SortBy";
            $rsd = mysqli_query($connect_read_var, $queryBroadbandList);
            while ($rs = mysqli_fetch_assoc($rsd)) {
                $resultArr[$count]['BroadbandID'] = $rs['broadbandID'];
                $resultArr[$count]['Name'] = $rs['name'];
                $resultArr[$count]['Price'] = $rs['lowest_price'];
                $resultArr[$count]['Speed'] = $rs['max_speed'];
                $resultArr[$count]['Mail'] = $rs['email'];
                $resultArr[$count]['PhoneNumber'] = $rs['contact_no'];
                $resultArr[$count]['Description'] = $rs['description'];
                $resultArr[$count]['Rating'] = $rs['rating'];
                $resultArr[$count]['Image'] = $rs['image'];
                $resultArr[$count]['Url'] = $rs['url'];
                $count++;
            }

            if ($count > 0) {
                $responseCode = "01";
                $responseMessage = 'Success';
            }

            //Encode Token Start
            $payload_info = array(
                "data" => $resultArr,
                "message" => $responseMessage
            );
            $encodeToken = encodeToken($payload_info, $secratekey);
            //Encode Token End

            mysqli_close($connect_read_var); //Read Connection Close

            echo json_encode(array("statusCode" => $responseCode, "recordCount" => $count, "response" => $encodeToken)); //Return Response

        } catch (PDOException $e) {
            echo json_encode(array("status" => "errors", "message_text" => $e->getMessage()), JSON_FORCE_OBJECT);
        }
    }
} // Class End Here


function broadbandListGet(array $data)
{
    $DashboardObject = new DashboardComponent;
    if ($data) {
        $DashboardObject->GetBroadbandListToken = $data;
        $DashboardObject->getBroadbandList();
    } else
        echo json_encode(array("status" => "error On Broadband List", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}
