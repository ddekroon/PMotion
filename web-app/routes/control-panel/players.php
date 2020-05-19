<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;
	
	$app->group('/control-panel/players', function () use ($app) {
		$app->get('/make-receipt[/{sportID}]', function (Request $request, Response $response) {

			$sportsController = new Controllers_SportsController($this->db, $this->logger);
			$allSports = $sportsController->getSports();

			$sport = Models_Sport::withID($this->db, $this->logger, $request->getAttribute('sportID'));
			
			return $this->view->render($response, "control-panel/players/make-receipt.phtml", [
				"request" => $request,
				"router" => $this->router,
				"allSports" => $allSports,
				"curSport" => $sport
			]);
		})->setName('cp-make-receipt');
		
	})->add($controlPanel)->add($authenticate);

	$app->post('/control-panel/players/generate-receipt', function (Request $request, Response $response) {
		$settings = $this->get('settings');
		$receiptsBasePath = $settings['root_path'] . 'Receipts';

		/* Parse values from POST */
		$allPostVars = $request->getParsedBody();

		$toName = $allPostVars['toName'];
		$sportName = $allPostVars['sportName'];

		$feeString = '';
		$totalPaid = round($allPostVars['totalPaid'], 2);
		$hst = round((($totalPaid / 1.13) - $totalPaid) * -1, 2);
		$subtotal = $totalPaid - $hst;

		$paidOrInvoiced = $allPostVars['paidOrInvoiced'];
		$receiptDate = strtotime($allPostVars['receiptDate']);
		$dt = new DateTime();
		$dt->setTimestamp($receiptDate);
		$receiptDateString = date_format($dt, "F jS, Y");

		$this->logger->debug($allPostVars['receiptDate']);
		$this->logger->debug($receiptDate);
		$this->logger->debug($receiptDateString);

		if(isset($allPostVars['seasonName'])) {
			$seasonString = ' '.$allPostVars['seasonName'][0];
			for($i = 1; $i < count($allPostVars['seasonName']); $i++) {
				$seasonString .= ' and ' . $allPostVars['seasonName'][$i];
			}
			$feeString = $sportName . ' ' . $seasonString . ' Fees';
		} else {
			$feeString = $_POST['miscFees'];
		}

		/* Save to file */
		$year = date('Y');
		$maxReceiptNum = 0;
		$pdf_dir = opendir($receiptsBasePath);

		while($filename = readdir($pdf_dir)) {
			$fileSplit = preg_split("/[-|.]/", $filename);

			if(strcmp(str_replace('Receipt', '', $fileSplit[0]), $year) == 0) {
				if(intval($fileSplit[1]) > $maxReceiptNum) {
					$maxReceiptNum = intval($fileSplit[1]);
				}
			}
		}
		closedir($pdf_dir);

		$newReceiptNum = $maxReceiptNum + 1;
		$fileName = 'Receipt' . $year . '-' . $newReceiptNum . '.pdf';
			
		$pdf = new Includes_PDFReceipt($this);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetFont('times','',12);
		$pdf->Cell(0,5,'Perpetual Motion',0,1,'C');
		$pdf->Cell(0,5,'78 Kathleen Street.',0,1,'C');
		$pdf->Cell(0,5,'Guelph, Ontario',0,1,'C');
		$pdf->Cell(0,5,'N1H 4Y3', 0, 1, 'C');
		$pdf->Cell(0,5,'519 - 222 - 0095', 0, 1, 'C');
		$pdf->Cell(0,5,'HST # 856323308', 0, 1, 'C');
		$pdf->Ln(15);
		$pdf->setX(27);
		$pdf->Cell(0, 5, $receiptDateString, 0, 1, 'L');
		$pdf->setX(27);
		$pdf->Cell(0, 5, '', 0, 1, 'L');
		$pdf->setX(27);
		$pdf->Cell(0, 5, 'To: ' . $toName, 0, 1, 'L');
		$pdf->setX(27);
		$pdf->Cell(0, 5, 'Re: ' . $feeString, 0, 1, 'L');
		$pdf->setX(27);
		$pdf->Cell(0, 5, '', 0, 1, 'L');
		$pdf->setX(27);
		$pdf->Cell(0, 5, 'Subtotal: $' . $subtotal, 0, 1, 'L');
		$pdf->setX(27);
		$pdf->Cell(0, 5, 'HST: $' . $hst, 0, 1, 'L');
		$pdf->setX(27);
		$pdf->Cell(0, 5, 'Total ' . $paidOrInvoiced . ': $' . $totalPaid, 0, 1, 'L');

		$pdf->Output($receiptsBasePath . DS . $fileName, 'F');

		/* Send file back to browser */
		$response = $response->withHeader('Content-Description', 'Player Receipt')
			->withHeader('Content-Type', 'application/octet-stream')
			->withHeader('Content-Disposition', 'attachment;filename="' . basename($fileName) . '"')
			->withHeader('Expires', '0')
			->withHeader('Cache-Control', 'must-revalidate')
			->withHeader('Pragma', 'public')
			->withHeader('Content-Length', filesize($receiptsBasePath . DS . $fileName));
		readfile($receiptsBasePath . DS . $fileName);

		return $response;
	})->setName('cp-generate-receipt');
	
?>