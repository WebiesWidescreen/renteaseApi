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

$userID = $_GET['UserID'];
$productOrderPlaceID = $_GET['ProductOrderPlaceID'];
$productData = '';
   
  $queryGetProductOrderList = "SELECT tblPOP.productOrderPlaceID, tblPOP.orderPlacedOn, tblPOP.userID, tblPOP.assignedToStaff, tblPOP.orderedAmount, tblPOP.deliveryCharge, tblPOP.couponDiscountAmount, tblPOP.isUsedWallet, tblPOP.usedWalletAmount, tblPOP.totalPaidAmount, tblPOP.transactionAmount, tblPOP.isDelivered, tblPOP.deliveredOn, tblPOP.isCancelled, tblPOP.cancelledOn, tblPOP.orderStatus, tblPOP.orderType, tblUL.locationAddress, tblUL.pinCode, tblPOP.usedPkPoints FROM tblProductOrderPlace tblPOP  INNER JOIN tblUserLocation tblUL ON tblPOP.delivaryAddressID = tblUL.userLocationID WHERE tblPOP.userID = '$userID' AND tblPOP.productOrderPlaceID = '$productOrderPlaceID' AND transactionStatus = 'SUCCESS' AND isTransactionSuccess = 1 ORDER BY tblPOP.productOrderPlaceID DESC LIMIT 0, 100";
                $rsd = mysqli_query($connect_read_var, $queryGetProductOrderList);
                while($rs = mysqli_fetch_assoc($rsd)) {
                    $ProductOrderPlaceID = $rs['productOrderPlaceID'];
                    $OrderPlacedOn = date('d-M-y h:i A', strtotime($rs['orderPlacedOn']));
                    $DeliveredEndOn = date('d-M-y h:i A', strtotime($rs['orderPlacedOn'].  '+ 3 days'));
                    $UserID = $rs['userID'];
                    $AssignedToStaff = $rs['assignedToStaff'];
                    $OrderedAmount = $rs['orderedAmount'];
                    $DeliveryCharge = $rs['deliveryCharge'];
                    $UsedPkPoints = $rs['usedPkPoints'];
                    $OrderTotalAmount = number_format($rs['orderedAmount'] + $rs['deliveryCharge'], 2);
                    $CouponDiscountAmount = $rs['couponDiscountAmount'];
                    $IsUsedWallet = $rs['isUsedWallet'];
                    $UsedWalletAmount = $rs['usedWalletAmount'];
                    $TotalPaidAmount = $rs['totalPaidAmount'];
                    $TransactionAmount = $rs['transactionAmount'];
                    $IsDelivered = $rs['isDelivered'];
                    $DeliveredOn = date('d-M-y h:i A', strtotime($rs['deliveredOn']));
                    $IsCancelled = $rs['isCancelled'];
                    $CancelledOn = date('d-M-y h:i A', strtotime($rs['cancelledOn']));
                    $OrderStatus = $rs['orderStatus'];
                    $OrderType = $rs['orderType'];
                    $DelivaryAddress = $rs['locationAddress'];
                    $PinCode = $rs['pinCode'];

                    $queryProductOrderList = "SELECT tblPOL.productOrderListID, tblPOL.productOrderPlaceID, tblPOL.productID, tblPOL.productClassifyID, tblPOL.productPriceChartID, tblPOL.mrpPrice, tblPOL.sellingPrice, tblPOL.isComboProduct, tblPOL.selectedOption, tblPOL.productQuanitity, tblPOL.typeOfProduct, tblPOL.isComboProduct, tblP.productName, tblP.productCategory FROM tblProductOrderList tblPOL INNER JOIN tblProduct tblP ON tblPOL.productID = tblP.productID WHERE tblPOL.productOrderPlaceID = '".$rs['productOrderPlaceID']."'";
                    $rsdProductOrderList = mysqli_query($connect_read_var, $queryProductOrderList);
                    $productAllName = '';
                    $productName = '';
                    while($rsProductList = mysqli_fetch_assoc($rsdProductOrderList)) {
                        if($rsProductList['isComboProduct'] == 1 && $rsProductList['typeOfProduct'] == 'ComboProduct') {
                            $queryCombo = "SELECT comboName, comboPrice FROM tblProductCombo WHERE productComboID = '".$rsProductList['productID']."'";
                            $rsdCombo = mysqli_query($connect_read_var, $queryCombo);
                            if($rsCombo = mysqli_fetch_assoc($rsdCombo)) {
                                $productName = strtolower($rsCombo['comboName']);
                                $ComboName = ucwords($productName);
                                $ProductTypeCategory = 'Combo';
                            }
                        }
                        if($rsProductList['isComboProduct'] == 1 && $rsProductList['typeOfProduct'] == 'ComboFlower'){
                            $queryComboFlower = "SELECT comboName, comboPrice FROM tblCombo WHERE comboID = '".$rsProductList['productID']."'";
                            $rsdComboFlower = mysqli_query($connect_read_var, $queryComboFlower);
                            if($rsComboFlower = mysqli_fetch_assoc($rsdComboFlower)) {
                                $productName = strtolower($rsComboFlower['comboName']);
                                $ComboName = ucwords($productName);
                                $ProductTypeCategory = 'ComboFlower';
                                $SellingPrice = $rsComboFlower['comboPrice'];
                                $sumOfOrderPrice += $rsComboFlower['comboPrice'];
                            }
                        } else {
                            $productName = strtolower($rsProductList['productName']);
                            $productName = ucwords($productName);
                            $ProductTypeCategory = $rsProductList['productCategory']; 
                            $ProductOrderListID = $rsProductList['productOrderListID'];
                            $ProductOrderPlaceID = $rsProductList['productOrderPlaceID'];
                            $ProductID = $rsProductList['productID'];
                            $ProductClassifyID = $rsProductList['productClassifyID'];
                            $ProductPriceChartID = $rsProductList['productPriceChartID'];
                            $IsComboProduct = $rsProductList['isComboProduct'];
                        }
                        $DelivaryAddress = $rsProductList['locationAddress'];
                        $MrpPrice = $rsProductList['mrpPrice'];
                        $SellingPrice = $rsProductList['sellingPrice'];
                        $productQuantity =  $rsProductList['productQuanitity'];
                        $selectedOption =  $rsProductList['selectedOption'];
                        $ProductQuanitity = $productQuantity;
                        $SelectedOption = $selectedOption;
                        if($productAllName != '') {
                            $productAllName = $productAllName.', '.$productName.' '.$selectedOption.' x '. $productQuantity;
                        } else {
                            $productAllName = $productName.' '.$selectedOption.' x '. $productQuantity;
                        }
                        $productData.='<tr>
                        <td> '.$productName.'</td>
                        <td>'.$ProductTypeCategory.'</td>
                        <td>'.$SelectedOption.'</td>
                        <td>₹ '.$MrpPrice.'</td>
                        <td>₹ '.$SellingPrice.'</td>
                        <td>'.$ProductQuanitity.'</td>
                      </tr>';
                    }
                    $AllProductName = $productAllName;
                }
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
                <td>Order Place ID:&nbsp;&nbsp;'.$ProductOrderPlaceID.'</td>
                <td>Order Set On:&nbsp;&nbsp; '.$OrderPlacedOn.'</td>
            </tr>
            <tr>
                <td>Total Paid Amount:&nbsp;&nbsp;₹ '.$TotalPaidAmount.'</td>
            </tr>
            <tr>
                <td>PinCode:&nbsp;&nbsp;<?php echo $PinCode?></td>
                <td>Order Type:&nbsp;&nbsp; '.$OrderType.'</td>
            </tr>
            <tr>
                <td>Address:&nbsp;&nbsp; '.$DelivaryAddress.'</td>
                <td></td>
            </tr>
        </tbody>
    </table><br><br>
     <table border = "1" cellpadding="2" align="center" style="font-size: 11px;">
        <thead>
            <tr>
                <th><b>Product</b></th>
                <th><b>Category</b></th>
                <th><b>Options</b></th>
                <th><b>MRP</b></th>
                <th><b>Selling Price</b></th>
                <th><b>QTY</b></th>
            </tr>
        </thead>
        <tbody>
          '.$productData.'
        </tbody>
     </table>
    </div>';

    mysqli_close($connect_read_var);

    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    ob_end_clean();
    $pdf->Output('OderDetails_Invoice.pdf', 'D');
?>
