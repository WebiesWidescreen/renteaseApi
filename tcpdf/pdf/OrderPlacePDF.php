<?php
include('../../readConfig.inc');

//Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('pookadai');
$pdf->SetTitle('Order Placed - PDF');
$pdf->SetSubject('Order Details');
$pdf->SetKeywords('Order Details, Print');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH,'', array(0,64,255), array(0,64,100));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 8, '', true);
// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage('A4');

$getUserID = $_GET['UserID'];
$orderModeID = $_GET['OrderModeID'];

$queryTimeZoneUpdate = "SET time_zone = '+05:30'";
$rsdTimeZoneUpdate = mysqli_query($connect_var,$queryTimeZoneUpdate);
date_default_timezone_set("Asia/Kolkata");
$getCreateDate = date('d-m-Y');
$getTime = date('H:i A');

$QtyOptions = '';
$DeliveryStatus = '';

$queryDeliveryList = "SELECT * FROM tblOrderMode tblOM INNER JOIN tblUserLocation tblUL ON tblOM.delivaryAddress = tblUL.userLocationID WHERE tblOM.userID = '$getUserID' AND tblOM.orderModeID = '$orderModeID' AND tblOM.transactionStatus = 'SUCCESS' AND tblOM.isTransactionSuccess = 1 ORDER BY tblOM.orderModeID DESC";
$rsd = mysqli_query($connect_read_var, $queryDeliveryList);
while($rs = mysqli_fetch_assoc($rsd)) {
    $OrderFromCustomerID = $rs['orderFromCustomerID'];
    $OrderModeID = $rs['orderModeID'];
    $OrderDate = date('d-M-y g:i a', strtotime($rs['orderSetOn']));
    $OrderFromDate = date('d-M-y', strtotime($rs['orderFromDate']));
    $DeliveryOnDate = date('d-M-y', strtotime($rs['orderToDate']));
    $IsDelivered = $rs['isDelivered'];
    $DeliveredOn = date('d-M-y h:i A', strtotime($rs['deliveredOn']));
    $DelivaryAddress = $rs['locationAddress'];
    $PinCode = $rs['pinCode'];
    $UserID = $rs['userID'];
    $IsOrderCancelled = $rs['isCancelled'];
    $CancelledOn = date('d-M-y h:i A', strtotime($rs['cancelledOn']));
    $OrderMode = $rs['orderMode'];
    $selectedDaysArr = array();

    if($rs['orderMode'] === 'Custom') {
        if($rs['isMonday'] == 1) {
            array_push($selectedDaysArr, 'Mon, ');
        }
        if($rs['isTuesday'] == 1) {
            array_push($selectedDaysArr, 'Tue, ');
        }
        if($rs['isWednesday'] == 1) {
            array_push($selectedDaysArr, 'Wed, ');
        }
        if($rs['isThursday'] == 1) {
            array_push($selectedDaysArr, 'Thu, ');
        }
        if($rs['isFriday'] == 1) {
            array_push($selectedDaysArr, 'Fri, ');
        }
        if($rs['isSaturday'] == 1) {
            array_push($selectedDaysArr, 'Sat, ');
        }
        if($rs['isSunday'] == 1) {
            array_push($selectedDaysArr, 'Sun, ');
        }
    }
    $SelectedDays = $selectedDaysArr;

    if($rs['orderMode'] === 'Custom' || $rs['orderMode'] === 'Daily') {
        $queryGetDefaultOrder = "SELECT * FROM tblDefaultOrder WHERE defaultOrderID = '".$rs['orderFromCustomerID']."' AND userID = '$getUserID'";
        $rsdDefaultOrder = mysqli_query($connect_read_var, $queryGetDefaultOrder);
        
        while($rsDefaultOrder = mysqli_fetch_assoc($rsdDefaultOrder)) {
            $queryProducts = "SELECT tblDOP.productID, tblDOP.productClassifyID, tblDOP.priceChartID, tblDOP.productQuanity, tblDOP.selectedItemSize,  tblDOP.productType, tblDOP.isComboProduct FROM  tblDefaultOrderProduct tblDOP WHERE defaultOrderID = '".$rsDefaultOrder['defaultOrderID'] ."'";
            $rsdProducts = mysqli_query($connect_read_var, $queryProducts);
            
            $sumOfOrderPrice = 0;
            while($rsProduct = mysqli_fetch_assoc($rsdProducts)) {
                $selectedCondion = $rsProduct['selectedItemSize'];
                $SelectedItem = $rsProduct['selectedItemSize'];
                $ProductQuanitity = $rsProduct['productQuanity'];

                $mrpPrice = 0;
                $sellingPrice = 0;
                if($rsProduct['isComboProduct'] == 1 && $rsProduct['productType'] == 'Combo') {
                    $queryCombo = "SELECT comboName, comboPrice FROM tblCombo WHERE comboID = '".$rsProduct['productID']."'";
                    $rsdCombo = mysqli_query($connect_read_var, $queryCombo);
                    if($rsCombo = mysqli_fetch_assoc($rsdCombo)) {
                        $productName = strtolower($rsCombo['comboName']);
                        $ProductName = ucwords($productName);
                        $ProductTypeCategory = 'Combo';
                        $SellingPrice = $rsCombo['comboPrice'];
                        $sumOfOrderPrice += $rsCombo['comboPrice'];

                    }
                } else {
                    $getNonCombo = "SELECT tblP.productName, tblP.productTypeCategory, tblPC.productImage1, tblPC.color, tblC.condition1Price, tblC.condition1MarketPrice, tblC.condition2Price, tblC.condition2MarketPrice, tblC.condition3Price, tblC.condition3MarketPrice FROM tblProduct tblP INNER JOIN tblProductClassify tblPC ON tblPC.productID = tblP.productID INNER JOIN tblPriceChart tblC ON tblPC.productClassifyID = tblC.productClassifyID WHERE tblP.productID = '".$rsProduct['productID']."'";
                    $rsdNonCombo = mysqli_query($connect_read_var, $getNonCombo);
                    if($rsNonCombo = mysqli_fetch_assoc($rsdNonCombo)) {
                        $productName = strtolower($rsNonCombo['productName']);
                        $ProductName = ucwords($productName);
                        $ProductTypeCategory = $rsNonCombo['productTypeCategory'];
                        $Color = $rsNonCombo['color'];
                        if($rsNonCombo['productTypeCategory'] === 'Tied'){
                            if($selectedCondion === '1') {
                                $sellingPrice = $rsNonCombo['condition1Price'];
                                $mrpPrice = $rsNonCombo['condition1MarketPrice'];
                            } else if($selectedCondion === '2') {
                                $sellingPrice = $rsNonCombo['condition2Price'];
                                $mrpPrice = $rsNonCombo['condition2MarketPrice'];
                            } else if($selectedCondion === '3') {
                                $sellingPrice = $rsNonCombo['condition3Price'];
                                $mrpPrice = $rsNonCombo['condition3MarketPrice'];
                            } else if($selectedCondion === '4') {
                                $sellingPrice = $rsNonCombo['condition4Price'];
                                $mrpPrice = $rsNonCombo['condition4MarketPrice'];
                            } else {
                                $perMozMrpPrice = $rsNonCombo['condition4MarketPrice'] /4;
                                $perMozSellingPrice = $rsNonCombo['condition4Price'] /4;
                                $sellingPrice = $rsNonCombo['condition4Price'] + $perMozSellingPrice;
                                $mrpPrice = $rsNonCombo['condition4MarketPrice'] + $perMozMrpPrice;
                            }
                        } else {
                            if($selectedCondion === '100') {
                                $sellingPrice = $rsNonCombo['condition1Price'];
                                $mrpPrice = $rsNonCombo['condition1MarketPrice'];
                            } else if($selectedCondion === '250') {
                                $sellingPrice = $rsNonCombo['condition2Price'];
                                $mrpPrice = $rsNonCombo['condition2MarketPrice'];
                            } else if($selectedCondion === '500') {
                                $sellingPrice = $rsNonCombo['condition3Price'];
                                $mrpPrice = $rsNonCombo['condition3MarketPrice'];
                            } else if($selectedCondion === '1000') {
                                $sellingPrice = $rsNonCombo['condition4Price'];
                                $mrpPrice = $rsNonCombo['condition4MarketPrice'];
                            } else {
                                if($selectedCondion < '250' && $rsNonCombo['condition1Price'] != 0) {
                                    $sellPrice = 100 / $rsNonCombo['condition1Price'];
                                    $marketPrice = 100 / $rsNonCombo['condition1MarketPrice'];
                                    $sellingPrice = round($selectedCondion / $sellPrice);
                                    $mrpPrice = round($selectedCondionselectCond / $marketPrice);

                                } else if($selectedCondion < '500' && $selectedCondion >= '250' && $rsNonCombo['condition2Price'] != 0) {
                                    $sellPrice = 250 / $rsNonCombo['condition2Price'];
                                    $marketPrice = 250 / $rsNonCombo['condition2MarketPrice'];
                                    $sellingPrice = round($selectedCondion / $sellPrice);
                                    $mrpPrice = round($selectedCondionselectCond / $marketPrice);

                                } else if($selectedCondion < '1000' && $selectedCondion >= '500' && $rsNonCombo['condition3Price'] != 0) {
                                    $sellPrice = 500 / $rsNonCombo['condition3Price'];
                                    $marketPrice = 500 / $rsNonCombo['condition3MarketPrice'];
                                    $sellingPrice = round($selectedCondion / $sellPrice);
                                    $mrpPrice = round($selectedCondionselectCond / $marketPrice);

                                } else if($selectedCondion > '1000' && $rsNonCombo['condition4Price'] != 0) {
                                    $sellPrice = 1000 / $rsNonCombo['condition4Price'];
                                    $marketPrice = 1000 / $rsNonCombo['condition4MarketPrice'];
                                    $sellingPrice = round($selectedCondion / $sellPrice);
                                    $mrpPrice = round($selectedCondionselectCond / $marketPrice);
                                }
                            }
                        }
                        $sumOfOrderPrice += $sellingPrice;
                        $MrpPrice = $mrpPrice;
                        $SellingPrice = $sellingPrice;
                    }
                }
                if ($ProductTypeCategory == 'Tied') {
                    $QtyOptions = 'Mozham';
                } else if ($ProductTypeCategory == 'Untied') {
                    $QtyOptions = 'Gms';
                } else if ($ProductTypeCategory == 'Combo') {
                    $QtyOptions = 'Combo';
                }
                $flowerdata .='<tr>
                <td>'.$ProductName.'</td>
                <td>'.$Color.'</td>
                <td>'.$ProductTypeCategory.'</td>
                <td>'.$SelectedItem.'('.$QtyOptions.')</td>
                <td>₹ '.$MrpPrice.'</td>
                <td>₹ '.$SellingPrice.'</td>
                <td>'.$ProductQuanitity.'</td>
                </tr>';
            }
            $OrderPrice = $sumOfOrderPrice;
        }
    } else {
        $queryCustomOrderDetails = "SELECT tblOFC.orderFromCustomerID, tblOFC.orderDate, tblOFC.deliveryOnDate, tblOFC.isDelivered, tblOFC.orderPrice, tblOFC.delivaryAddress, tblOFC.userID, tblOFC.isOrderCancelled, tblOFC.cancelledOn, tblOFC.successfulDelivery FROM tblOrderFromCustomer tblOFC WHERE tblOFC.orderFromCustomerID = '".$rs['orderFromCustomerID']."' AND tblOFC.userID = '$getUserID' ORDER BY tblOFC.orderFromCustomerID DESC";
        $rsdCustomOrder = mysqli_query($connect_read_var, $queryCustomOrderDetails);
        while($rsCustomOrder = mysqli_fetch_assoc($rsdCustomOrder)) {

            $OrderPrice = $rsCustomOrder['orderPrice'];
            $queryGetProductDetails = "SELECT listOrderID, productID, mrpPrice, sellingPrice, selectedItemCond, productQuanitity, isComboProduct, productType FROM tblListOrder WHERE orderFromCustomerID = '".$rsCustomOrder['orderFromCustomerID']."'";
            $rsdProduct = mysqli_query($connect_read_var, $queryGetProductDetails);

            while($rsProduct = mysqli_fetch_assoc($rsdProduct)) {
                $MrpPrice = $rsProduct['mrpPrice'];
                $SellingPrice = $rsProduct['sellingPrice'];
                $SelectedItem = $rsProduct['selectedItemCond'];
                $ProductQuanitity = $rsProduct['productQuanitity'];

                if($rsProduct['isComboProduct'] == 1 && $rsProduct['productType'] == 'Combo') {
                    $queryCombo = "SELECT comboName, comboPrice FROM tblCombo WHERE comboID = '".$rsProduct['productID']."'";
                    $rsdCombo = mysqli_query($connect_read_var, $queryCombo);
                    if($rsCombo = mysqli_fetch_assoc($rsdCombo)) {
                        $productName = strtolower($rsCombo['comboName']);
                        $ProductName = ucwords($productName);
                        $ProductTypeCategory = 'Combo';

                    }
                } else {
                    $getNonCombo = "SELECT tblP.productName, tblP.productTypeCategory, tblPC.color, tblPC.productImage1 FROM tblProduct tblP INNER JOIN tblProductClassify tblPC ON tblPC.productID = tblP.productID WHERE tblP.productID = '".$rsProduct['productID']."'";
                    $rsdNonCombo = mysqli_query($connect_read_var, $getNonCombo);
                    if($rsNonCombo = mysqli_fetch_assoc($rsdNonCombo)) {
                        $productName = strtolower($rsNonCombo['productName']);
                        $ProductName = ucwords($productName);
                        $ProductTypeCategory = $rsNonCombo['productTypeCategory'];
                        $Color = $rsNonCombo['color'];
                    }
                }
                if ($ProductTypeCategory == 'Tied') {
                    $QtyOptions = 'Mozham';
                } else if ($ProductTypeCategory == 'Untied') {
                    $QtyOptions = 'Gms';
                } else if ($ProductTypeCategory == 'Combo') {
                    $QtyOptions = 'Combo';
                }
                $flowerdata .='<tr>
                <td>'.$ProductName.'</td>
                <td>'.$Color.'</td>
                <td>'.$ProductTypeCategory.'</td>
                <td>'.$SelectedItem.'('.$QtyOptions.')</td>
                <td>₹ '.$MrpPrice.'</td>
                <td>₹ '.$SellingPrice.'</td>
                <td>'.$ProductQuanitity.'</td>
                </tr>';
            }
        }
    }

    if ($IsDelivered == '0') {
        $DeliveryStatus = 'Yet To Delivered';
    } else if ($IsDelivered == '1') {
        $DeliveryStatus = 'Delivered';
    }
}

//$domainAddress = "http://localhost:8888/pookadaiapi/";
$domainAddress = "https://api.pookadai.co.in/";


$html='<div>
        <div style="font-size: 15px; font-weight: bold; margin-top:-20px;"> <img src="logo.png" style="width: 150px; height: 50px;margin: 20x;" align="left" /> <span></span></div> <div style="font-size: 14px; font-weight: bold;text-align: center;">Invoice</div><br><br>
        <table>
            <thead>
                <tr>
                    <th><b>Order Details</b></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Order Place ID:&nbsp;&nbsp;'.$OrderFromCustomerID.'</td>
                    <td>Order Set On:&nbsp;&nbsp;'.$OrderDate.'</td>
                </tr>
                <tr>
                    <td>Order Price:&nbsp;&nbsp;₹ '.$OrderPrice.'</td>
                    <td>Order Mode:&nbsp;&nbsp;'.$OrderMode.'</td>
                </tr>
                <tr>
                    <td>PinCode:&nbsp;&nbsp;'.$PinCode.'</td>
                    <td>Delivery Status:&nbsp;&nbsp;'.$DeliveryStatus.'</td>
                </tr>
                <tr>
                    <td>Address:&nbsp;&nbsp;'.$DelivaryAddress.'</td>
                    <td></td>
                </tr>
            </tbody>
        </table><br><br>
         <table border = "1" cellpadding="2" align="center" style="font-size: 11px;">
            <thead>
                <tr>
                    <th><b>Product</b></th>
                    <th><b>Color</b></th>
                    <th><b>Category</b></th>
                    <th><b>Options</b></th>
                    <th><b>MRP</b></th>
                    <th><b>Selling Price</b></th>
                    <th><b>QTY</b></th>
                </tr>
            </thead>
            <tbody>
                '.$flowerdata.'
            </tbody>
         </table>
        </div>';
            
mysqli_close($connect_read_var);
mysqli_close($connect_var);

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
$pdf->Output('OderDetails_Invoice.pdf', 'D');