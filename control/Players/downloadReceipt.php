<?php /*****************************************
File: updatePlayer.php
Creator: Derek Dekroon
Created: July 17/2012
When linked to it should automatically download the correct pdf receipt.
******************************************/
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).$pdfPage);

class PDF extends FPDF
{
	// Page header
	function Header() {
		// Logo
		$this->Image(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'Logos/Perpetualmotionlogo.jpg',27,25,155);
		// Arial bold 15
		$this->SetFont('times','',15);
		// Line break
		$this->Ln(55);
	}
	
	// Page footer
	function Footer() {
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('times','',8);
		// Page number
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

function printDate() {
	global $selectedDay, $selectedMonth, $selectedYear;
	$suffix = '';
	if($selectedDay < 11 || $selectedDay > 13){
         switch($selectedDay % 10){
            case 1: $suffix =  'st';
				break;
            case 2: $suffix =  'nd';
				break;
            case 3: $suffix =  'rd';
				break;
        }
    }
	if($suffix == '') {
    	$suffix = 'th';
	}
	
	return date('F', strtotime($_POST['date'])).' '.$selectedDay.$suffix.', '.$selectedYear;
}

if(isset($_POST['inputData'])) {
	$dateTokens = explode('-', $_POST['date']);
	$selectedDay = $dateTokens[2];
	$selectedMonth = $dateTokens[1];
	$selectedYear = $dateTokens[0];
	$sportID = $_POST['sportID'];
	
	if($sportID == 1) {
		$sportName = 'Ultimate Frisbee';
	} else if($sportID == 2) {
		$sportName = 'Beach Volleyball';
	} else if($sportID == 3) {
		$sportName = 'Flag Football';
	} else if($sportID == 4) {
		$sportName = 'Soccer';
	} else {
		$sportName = '';
	}
	$toName = $_POST['toName'];
	$totalPaid = $_POST['totalPaid'];
	if(isset($_POST['season'])) {
		$seasonString = ' '.$_POST['season'][0];
		for($i = 1; $i < count($_POST['season']); $i++) {
			$seasonString .= ' and '.$_POST['season'][$i];
		}
		$feeString = $sportName.$seasonString.' Fees';
	} else {
		$feeString = $_POST['miscFees'];
	}
	if($feeString == ' Fees' || $toName == '' || $totalPaid == '') {
		print 'ERROR, data missing<br />';
		exit(0);
	}
} else {
	$container = new Container('Download Receipt');
	print 'ERROR no data given<br />';
	$container->printFooter();
	exit(0);
}

//Hasn't exited yet, make the pdf and download it

$year = date('Y');
$maxReciptNum = 0;

$pdf_dir = opendir('Receipts');
while($filename = readdir($pdf_dir)){
	$fileSplit = preg_split("/[-|.]/", $filename);
	if(strcmp(str_replace('Receipt', '', $fileSplit[0]), $year) == 0) {
		if(intval($fileSplit[1]) > $maxReceiptNum) {
			$maxReciptNum = intval($fileSplit[1]);
		}
	}
}
closedir($pdf_dir);

$newReceiptNum = $maxReciptNum + 1;
$fileName = 'Receipt'.$year.'-'.$newReceiptNum.'.pdf';

	
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('times','',12);
$pdf->Cell(0,5,'Perpetual Motion',0,1,'C');
$pdf->Cell(0,5,'78 Kathleen Street.',0,1,'C');
$pdf->Cell(0,5,'Guelph, Ontario',0,1,'C');
$pdf->Cell(0,5,'N1H 4Y3',0,1,'C');
$pdf->Cell(0,5,'519 - 222 - 0095',0,1,'C');
$pdf->Cell(0,5,'HST # 856323308',0,1,'C');
$pdf->Ln(15);
$pdf->setX(27);
$pdf->Cell(0,5,printDate(),0,1,'L');
$pdf->setX(27);
$pdf->Cell(0,5,'',0,1,'L');
$pdf->setX(27);
$pdf->Cell(0,5,'To: '.$toName,0,1,'L');
$pdf->setX(27);
$pdf->Cell(0,5,'Re: '.$feeString,0,1,'L');
$pdf->setX(27);
$pdf->Cell(0,5,'',0,1,'L');
$pdf->setX(27);
$pdf->Cell(0,5,'Total Paid: $'.$totalPaid.' (HST Included)',0,1,'L');
//$pdf->Output();
$pdf->Output('Receipts'.DIRECTORY_SEPARATOR.$fileName, 'F');
$pdf->Output($fileName, 'D'); ?>