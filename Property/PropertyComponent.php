<?php
class PropertyComponent
{
    public $AddPropertyToken;
    public $UpdatePropertyToken;
    public $DeletePropertyToken;
    public $GetPropertyListToken;
    public $AddPropertyUnitToken;
    public $UpdatePropertyUnitToken;
    public $DeletePropertyUnitToken;
    public $GetPropertyUnitListToken;

    // Start Property
    public function addProperty()
    {
        include('config.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';
        try {

            //Decode Token Start
            $secratekey = "PROPERTY";
            $decodeVal = decodeToken($this->AddPropertyToken['ADD_PROPERTY'], $secratekey);
            //Decode Token End

            $rsd = false;
            $lastInsertPropertyID = '';
            $address1 = '';
            $address2 = '';

            if (isset($decodeVal->addressLine1))
                $address1 = $decodeVal->addressLine1;

            if (isset($decodeVal->addressLine2))
                $address2 = $decodeVal->addressLine2;

            $address = $address1 . " " . $address2;

            $queryCreateProperty = "INSERT INTO tbl_property(userID, propertyName, propertyType, managerName, managerPhone, address, city, country, state, zipCode) VALUES ('$decodeVal->userID', '$decodeVal->propertyName', '$decodeVal->propertyType', '$decodeVal->managerName', '$decodeVal->managerContactNo', '$address', '$decodeVal->city', '$decodeVal->country', '$decodeVal->state', '$decodeVal->zipcode')";
            $rsd = mysqli_query($connect_var, $queryCreateProperty);
            $lastInsertPropertyID =  mysqli_insert_id($connect_var);


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

            echo json_encode(array("statusCode" => $responseCode, "response" => $encodeToken, "propertyID" => $lastInsertPropertyID)); //Return Response

        } catch (PDOException $e) {
            echo json_encode(array("status" => "errors", "message_text" => $e->getMessage()), JSON_FORCE_OBJECT);
        }
    }

    public function updateProperty()
    {
        include('config.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';
        try {

            //Decode Token Start
            $secratekey = "PROPERTY";
            $decodeVal = decodeToken($this->UpdatePropertyToken['UPDATE_PROPERTY'], $secratekey);
            //Decode Token End

            $address1 = '';
            $address2 = '';
            $property = '';

            if (isset($decodeVal->addressLine1))
                $address1 = $decodeVal->addressLine1;

            if (isset($decodeVal->addressLine2))
                $address2 = $decodeVal->addressLine2;

            $address = $address1 . " " . $address2;

            if (isset($decodeVal->propertyName))
                $property .= "propertyName='$decodeVal->propertyName', ";

            if (isset($decodeVal->propertyType))
                $property .= "propertyType='$decodeVal->propertyType', ";

            if (isset($decodeVal->managerName))
                $property .= "managerName='$decodeVal->managerName', ";

            if (isset($decodeVal->managerContactNo))
                $property .= "managerPhone='$decodeVal->managerContactNo', ";

            $property .= "address='$address', ";

            if (isset($decodeVal->city))
                $property .= "city='$decodeVal->city', ";

            if (isset($decodeVal->country))
                $property .= "country='$decodeVal->country', ";

            if (isset($decodeVal->state))
                $property .= "state='$decodeVal->state', ";

            if (isset($decodeVal->zipcode))
                $property .= "zipcode='$decodeVal->zipcode', ";

            $property .= "modifiedOn=NOW() ";


            $queryUpdateProperty = "UPDATE tbl_property SET " . trim($property, ",") . " WHERE propertyID = '$decodeVal->propertyID'";
            $rsd = mysqli_query($connect_var, $queryUpdateProperty);

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

    public function deleteProperty()
    {
        include('config.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';
        try {

            //Decode Token Start
            $secratekey = "PROPERTY";
            $decodeVal = decodeToken($this->DeletePropertyToken['DELETE_PROPERTY'], $secratekey);
            //Decode Token End

            $queryUpdatePropertyIsView = "UPDATE tbl_property SET isView ='$decodeVal->isView', modifiedOn=NOW() WHERE propertyID = '$decodeVal->propertyID'";
            $rsd = mysqli_query($connect_var, $queryUpdatePropertyIsView);

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

    public function getPropertyList()
    {
        include('readConfig.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';

        $resultArr = array();
        try {

            //Decode Token Start
            $secratekey = "PROPERTY";
            $decodeVal = decodeToken($this->GetPropertyListToken['GET_PROPERTY_LIST'], $secratekey);
            //Decode Token End

            $rsd = false;
            $count = 0;

            $queryPropertyList = "SELECT tblP.propertyID, tblP.userID, tblP.propertyName, tblP.propertyType, tblP.managerName, tblP.managerPhone, tblP.address, tblP.city, tblP.country, tblP.state, tblP.zipCode, tblP.createdOn FROM tbl_property tblP WHERE tblP.userID = '$decodeVal->userID' AND tblP.isView = 1 ORDER BY tblP.propertyID DESC";
            $rsd = mysqli_query($connect_read_var, $queryPropertyList);
            while ($rs  = mysqli_fetch_assoc($rsd)) {
                $resultArr[$count]['PropertyID'] = $rs['propertyID'];
                $resultArr[$count]['UserID'] = $rs['userID'];
                $resultArr[$count]['PropertyName'] = $rs['propertyName'];
                $resultArr[$count]['PropertyType'] = $rs['propertyType'];
                $resultArr[$count]['ManagerName'] = $rs['managerName'];
                $resultArr[$count]['ManagerPhone'] = $rs['managerPhone'];
                $resultArr[$count]['Address'] = $rs['address'];
                $resultArr[$count]['City'] = $rs['city'];
                $resultArr[$count]['Country'] = $rs['country'];
                $resultArr[$count]['State'] = $rs['state'];
                $resultArr[$count]['ZipCode'] = $rs['zipCode'];
                $resultArr[$count]['CreatedOn'] = date("d-m-Y h:i A", strtotime($rs['createdOn']));
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

            echo json_encode(array("statusCode" => $responseCode, "response" => $encodeToken, "totalCount" => $count)); //Return Response

        } catch (PDOException $e) {
            echo json_encode(array("status" => "errors", "message_text" => $e->getMessage()), JSON_FORCE_OBJECT);
        }
    }
    // End Property

    // Start Property Unit
    public function addPropertyUnit()
    {
        include('config.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';
        try {

            //Decode Token Start
            $secratekey = "PROPERTY_UNIT";
            $decodeVal = decodeToken($this->AddPropertyUnitToken['ADD_PROPERTY_UNIT'], $secratekey);
            //Decode Token End

            $rsd = false;
            $lastInsertUnitID = '';

            $queryCreatePropertyUnit = "INSERT INTO tbl_unit(propertyID, unitName, unitType, rentCategory, electricityCategory, waterCategory, maintanceCategory) VALUES ('$decodeVal->propertyID', '$decodeVal->unitName', '$decodeVal->unitType', '$decodeVal->rentCategory', '$decodeVal->electricityCategory', '$decodeVal->waterCategory', '$decodeVal->maintanceCategory')";
            $rsd = mysqli_query($connect_var, $queryCreatePropertyUnit);
            $lastInsertUnitID =  mysqli_insert_id($connect_var);


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

            echo json_encode(array("statusCode" => $responseCode, "response" => $encodeToken, "unitID" => $lastInsertUnitID)); //Return Response

        } catch (PDOException $e) {
            echo json_encode(array("status" => "errors", "message_text" => $e->getMessage()), JSON_FORCE_OBJECT);
        }
    }

    public function updatePropertyUnit()
    {
        include('config.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';
        try {

            //Decode Token Start
            $secratekey = "PROPERTY_UNIT";
            $decodeVal = decodeToken($this->UpdatePropertyUnitToken['UPDATE_PROPERTY_UNIT'], $secratekey);
            //Decode Token End

            $propertyUnit = '';

            if (isset($decodeVal->unitName))
                $propertyUnit .= "unitName='$decodeVal->unitName', ";

            if (isset($decodeVal->unitType))
                $propertyUnit .= "unitType='$decodeVal->unitType', ";

            if (isset($decodeVal->rentCategory))
                $propertyUnit .= "rentCategory='$decodeVal->rentCategory', ";

            if (isset($decodeVal->electricityCategory))
                $propertyUnit .= "electricityCategory='$decodeVal->electricityCategory', ";

            if (isset($decodeVal->waterCategory))
                $propertyUnit .= "waterCategory='$decodeVal->waterCategory', ";

            if (isset($decodeVal->maintanceCategory))
                $propertyUnit .= "maintanceCategory='$decodeVal->maintanceCategory', ";

            $propertyUnit .= "modifiedOn=NOW() ";


            $queryUpdatePropertyUnit = "UPDATE tbl_unit SET " . trim($propertyUnit, ",") . " WHERE unitID = '$decodeVal->unitID'";
            $rsd = mysqli_query($connect_var, $queryUpdatePropertyUnit);

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

    public function deletePropertyUnit()
    {
        include('config.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';
        try {

            //Decode Token Start
            $secratekey = "PROPERTY_UNIT";
            $decodeVal = decodeToken($this->DeletePropertyUnitToken['DELETE_PROPERTY_UNIT'], $secratekey);
            //Decode Token End

            $queryUpdatePropertyUnitIsView = "UPDATE tbl_unit SET isView ='$decodeVal->isView', modifiedOn=NOW() WHERE unitID = '$decodeVal->unitID'";
            $rsd = mysqli_query($connect_var, $queryUpdatePropertyUnitIsView);

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

    public function getPropertyUnitList()
    {
        include('readConfig.inc');
        header('Content-Type: application/json');

        $responseCode = "100";
        $responseMessage = 'Failed';

        $resultArr = array();
        try {

            //Decode Token Start
            $secratekey = "PROPERTY_UNIT";
            $decodeVal = decodeToken($this->GetPropertyUnitListToken['GET_PROPERTY_UNIT_LIST'], $secratekey);
            //Decode Token End

            $rsd = false;
            $count = 0;

            $queryPropertyUnitList = "SELECT tblU.unitID, tblU.propertyID, tblU.unitName, tblU.unitType, tblU.rentCategory, tblU.electricityCategory, tblU.waterCategory, tblU.maintanceCategory, tblU.createdOn FROM tbl_unit tblU WHERE tblU.propertyID = '$decodeVal->propertyID' AND tblU.isView = 1 ORDER BY tblU.unitID DESC";
            $rsd = mysqli_query($connect_read_var, $queryPropertyUnitList);
            while ($rs  = mysqli_fetch_assoc($rsd)) {
                $resultArr[$count]['UnitID'] = $rs['unitID'];
                $resultArr[$count]['PropertyID'] = $rs['propertyID'];
                $resultArr[$count]['UnitName'] = $rs['unitName'];
                $resultArr[$count]['UnitType'] = $rs['unitType'];
                $resultArr[$count]['RentCategory'] = $rs['rentCategory'];
                $resultArr[$count]['ElectricityCategory'] = $rs['electricityCategory'];
                $resultArr[$count]['WaterCategory'] = $rs['waterCategory'];
                $resultArr[$count]['MaintanceCategory'] = $rs['maintanceCategory'];
                $resultArr[$count]['CreatedOn'] = date("d-m-Y h:i A", strtotime($rs['createdOn']));
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

            echo json_encode(array("statusCode" => $responseCode, "response" => $encodeToken, "totalCount" => $count)); //Return Response

        } catch (PDOException $e) {
            echo json_encode(array("status" => "errors", "message_text" => $e->getMessage()), JSON_FORCE_OBJECT);
        }
    }
    // End Property Unit
} // Class End Here

// Start Property
function propertyAdd(array $data)
{
    $PropertyObject = new PropertyComponent;
    if ($data) {
        $PropertyObject->AddPropertyToken = $data;
        $PropertyObject->addProperty();
    } else
        echo json_encode(array("status" => "error On Property Add", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}

function propertyUpdate(array $data)
{
    $PropertyObject = new PropertyComponent;
    if ($data) {
        $PropertyObject->UpdatePropertyToken = $data;
        $PropertyObject->updateProperty();
    } else
        echo json_encode(array("status" => "error On Property Update", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}

function propertyDelete(array $data)
{
    $PropertyObject = new PropertyComponent;
    if ($data) {
        $PropertyObject->DeletePropertyToken = $data;
        $PropertyObject->deleteProperty();
    } else
        echo json_encode(array("status" => "error On Property Delete", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}

function propertyListGet(array $data)
{
    $PropertyObject = new PropertyComponent;
    if ($data) {
        $PropertyObject->GetPropertyListToken = $data;
        $PropertyObject->getPropertyList();
    } else
        echo json_encode(array("status" => "error On Property List", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}
// End Property

// Start Property Unit
function propertyUnitAdd(array $data)
{
    $PropertyObject = new PropertyComponent;
    if ($data) {
        $PropertyObject->AddPropertyUnitToken = $data;
        $PropertyObject->addPropertyUnit();
    } else
        echo json_encode(array("status" => "error On Property Unit Add", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}

function propertyUnitUpdate(array $data)
{
    $PropertyObject = new PropertyComponent;
    if ($data) {
        $PropertyObject->UpdatePropertyUnitToken = $data;
        $PropertyObject->updatePropertyUnit();
    } else
        echo json_encode(array("status" => "error On Property Unit Update", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}

function propertyUnitDelete(array $data)
{
    $PropertyObject = new PropertyComponent;
    if ($data) {
        $PropertyObject->DeletePropertyUnitToken = $data;
        $PropertyObject->deletePropertyUnit();
    } else
        echo json_encode(array("status" => "error On Property Unit Delete", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}

function propertyUnitListGet(array $data)
{
    $PropertyObject = new PropertyComponent;
    if ($data) {
        $PropertyObject->GetPropertyUnitListToken = $data;
        $PropertyObject->getPropertyUnitList();
    } else
        echo json_encode(array("status" => "error On Property Unit List", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}
// End Property Unit
