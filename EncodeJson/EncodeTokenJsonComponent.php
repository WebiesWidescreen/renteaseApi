<?php
class EncodeTokenJsonComponent
{
    public $encodeJsonObject;
    public $decodeJsonObject;
    public $secratekey;

    public function loanEncodeTockenJsonDetails(array $data)
    {
        $this->encodeJsonObject = $data;
        $this->secratekey = $data['Secratekey'];
        return true;
    }

    public function loanDecodeTockenJsonDetails(array $data)
    {
        $this->decodeJsonObject = $data;
        $this->secratekey = $data['Secratekey'];
        return true;
    }

    public function generateTockenUsingJson()
    {
        header('Content-Type: application/json');
        $status = 01;
        try {
            // ENCODE START 
            $encodeToken = encodeToken($this->encodeJsonObject, $this->secratekey);
            // ENCODE END 

            if ($status === 01)
                echo json_encode(array("StatusCode" => "01", "Response" => $encodeToken), JSON_FORCE_OBJECT);
            else
                echo json_encode(array("StatusCode" => "02", "Response" => $encodeToken), JSON_FORCE_OBJECT);
        } catch (PDOException $e) {
            echo json_encode(array("status" => "errors", "message_text" => $e->getMessage()), JSON_FORCE_OBJECT);
        }
    }


    public function decodeJsonTocken()
    {
        header('Content-Type: application/json');
        $status = 01;
        try {
            // ENCODE START
            $encodeToken = '';
            $decodeToken = decodeToken($this->decodeJsonObject['DecodeToken'], $this->secratekey);
            // ENCODE END 
            if ($status === 01)
                echo json_encode(array("StatusCode" => "01", "Response" => $decodeToken), JSON_FORCE_OBJECT);
            else
                echo json_encode(array("StatusCode" => "02", "Response" => $encodeToken), JSON_FORCE_OBJECT);
        } catch (PDOException $e) {
            echo json_encode(array("status" => "errors", "message_text" => $e->getMessage()), JSON_FORCE_OBJECT);
        }
    }
}

function encodeJwtJson(array $data)
{
    $EncodeTockenObject = new EncodeTokenJsonComponent;
    if ($EncodeTockenObject->loanEncodeTockenJsonDetails($data)) {
        $EncodeTockenObject->generateTockenUsingJson();
    } else
        echo json_encode(array("status" => "error On Profile Creation ", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}

function decodeJwtJson(array $data)
{
    $decodeJsonObject = new EncodeTokenJsonComponent;
    if ($decodeJsonObject->loanDecodeTockenJsonDetails($data)) {
        $decodeJsonObject->decodeJsonTocken();
    } else
        echo json_encode(array("status" => "error On Profile Creation ", "message_text" => "Invalid Input Parameters"), JSON_FORCE_OBJECT);
}
