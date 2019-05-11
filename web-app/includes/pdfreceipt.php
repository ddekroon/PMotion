<?php
/*******************************************************************************
* PDF Receipt                                                                  *
*                                                                              *
* Version: 1.00                                                                *
* Date:    2019-05-10                                                          *
* Author:  Derek Dekroon                                                       *
*******************************************************************************/

class Includes_PDFReceipt extends Includes_FPDF
{
	private $container;

	function __construct($container, $orientation='P', $unit='mm', $size='A4')
	{
		$this->container = $container;
		parent::__construct($orientation, $unit, $size);
	}

	// Page header
	function Header() {
		$settings = $this->container->get('settings');

		// Logo
		$this->Image($settings['root_path'] . DS . 'Logos/Perpetualmotionlogo.jpg', 27, 25, 155);

		// Arial bold 15
		$this->SetFont('times', '', 15);

		// Line break
		$this->Ln(55);
	}
	
	// Page footer
	function Footer() {
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('times', '', 8);
		// Page number
		$this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
	}
}