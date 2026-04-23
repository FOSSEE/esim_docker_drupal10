<?php

	function generate_pdf()
	{       $mpath = drupal_get_path('module', 'textbook_companion');
		require($mpath.'/pdf/fpdf/fpdf.php');
		require($mpath.'/pdf/phpqrcode/qrlib.php');
		$user = \Drupal::currentUser();
		$x = $user->uid;
		$proposal_id = arg(2);
		$query2 = \Drupal::database()->query("SELECT * FROM {textbook_companion_preference} WHERE approval_status=1 AND  proposal_id= :prop_id", array(':prop_id' => $proposal_id));
		$data2 = $query2->fetchObject();
		$query3 = \Drupal::database()->query("SELECT * FROM {textbook_companion_proposal} WHERE id= :prop_id", array(':prop_id' =>$proposal_id));
		$data3 = $query3->fetchObject();
  		
		$gender = array('salutation' => 'Mr. /Ms.', 'gender' => 'He/She');
		if($data3->gender){
			if($data3->gender == 'M'){
			 	$gender = array('salutation' => 'Mr.', 'gender' => 'He');
			
			}else{
				$gender = array('salutation' => 'Ms.', 'gender' => 'She');
			}
		}
		
			$pdf=new FPDF('L', 'mm', 'Letter');
			if (!$pdf)
			{
				echo "Error!";
			}
			$pdf->AddPage();
			

			$pdf->Rect(5, 5, 267, 207, 'D'); //For A4


			$pdf->SetMargins(18,1,18);
			
			$pdf->Line(7.0,7.0,270.0,7.0);
			$pdf->Line(7.0,7.0,7.0,210.0);
			$pdf->Line(270.0,210.0,270.0,7.0);
			$pdf->Line(7.0,210.0,270.0,210.0);
			$path = drupal_get_path('module', 'textbook_companion');
			$image1=$mpath."/pdf/images/esim_logo.png";
			$pdf->Ln(15);
				$pdf->Cell(200,8, $pdf->Image($image1 ,120, 19, 0, 28), 0, 1, 'C' );
				$pdf->Ln(20);
				$pdf->SetFont('Arial','BI',25);
				$pdf->SetTextColor(139,69,19);
				$pdf->Cell(240,8,'Certificate of Participation', '0',1,'C');
		        $pdf->Ln(4);
			$pdf->SetFont('Arial','BI',12);
			$pdf->SetTextColor(0,0,0);
			$pdf->Cell(240,8,'This is to certify that', '0','1','C');
			$pdf->Ln(4);
			$pdf->SetFont('Arial','BI',25);
			$pdf->SetTextColor(139,69,19);
			$pdf->Cell(240,8,$data3->full_name, '0','1','C');
			$pdf->Ln(5);
			$pdf->SetFont('Arial','I',12);
		if(strtolower($data3->branch)!="others")
		{
		
		$pdf->SetTextColor(0,0,0);
		
		$pdf->Cell(240,8,'from '.$data3->university.' has successfully','0','1','C');
		$pdf->Ln(0);
		$pdf->Cell(240,8,'completed an eSim Textbook Companion', '0','1','C');
		$pdf->Ln(0);
		$pdf->Cell(240,8,'He/She has created eSim project files for all the solved simulation example in the', '0','1','C');
		$pdf->Ln(0);
		$pdf->Cell(240,8,'textbook: '.$data2->book.', Author: '.$data2->author.'.', '0','1','C');
		$pdf->Ln(0);
		}
		else
		{
		
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(240,8,'from '.$data3->university.' has successfully','0','1','C');
		$pdf->Ln(0);
		$pdf->Cell(240,8,'completed a eSim Textbook Companion', '0','1','C');
		$pdf->Ln(0);
		$pdf->Cell(240,8,'He/She has created eSim project files for all the solved simulation example in the', '0','1','C');
		$pdf->Ln(0);
		$pdf->Cell(240,8,'textbook: '.$data2->book.', Author: '.$data2->author.'.', '0','1','C');
		$pdf->Ln(0);
		

		}	

		
			$proposal_get_id=0;
			$UniqueString="";
			$tempDir =$path."/pdf/temp_prcode/" ;
			//$UniqueString=generateRandomString();
			
			$query = \Drupal::database()->select('textbook_companion_qr_code');
	  			$query->fields('textbook_companion_qr_code', array(
				'qr_code',
            
        			));
       
        		//$query->condition('qr_code', $UniqueString);
			$query->condition('proposal_id', $proposal_id);
        		$result = $query->execute();
			$data = $result->fetchObject();	
			$DBString=$data->qr_code;
			$proposal_get_id=$data->proposal_id;
			
			if($DBString==""||$DBString=="null"){
				$UniqueString=generateRandomString();
				$query = "
				INSERT INTO textbook_companion_qr_code
				(proposal_id,qr_code)
				VALUES
				(:proposal_id,:qr_code)
				";
                    		$args = array(
                        		":proposal_id" => $proposal_id,
                        		":qr_code" => $UniqueString
                   		 );
                    		/* storing the row id in $result */
                   		 $result = \Drupal::database()->query($query, $args, $query);
			

			}else{
				$UniqueString=$DBString;

			}


			$codeContents = "http://esim.fossee.in/certificates/verify/".$UniqueString;

			$fileName = 'generated_qrcode.png';

			$pngAbsoluteFilePath = $tempDir.$fileName;
			$urlRelativeFilePath = $path."/pdf/temp_prcode/".$fileName;
			QRcode::png($codeContents, $pngAbsoluteFilePath); 

			$pdf->Cell(240,4,'','0','1','C');
			$pdf->SetX(95);	
			$pdf->write(0,'The work done is available at ');
			$pdf->SetFont('','U');
			$pdf->SetTextColor(139,69,19);
			$pdf->write(0,'http://esim.fossee.in/','http://esim.fossee.in/');
			$pdf->SetFont('','');
			$pdf->SetTextColor(0,0,0);
			$pdf->write(0,'.','.');
			
			$pdf->Ln(5);
			
			$pdf->SetX(198);
			$pdf->SetFont('','');
			$pdf->SetTextColor(0,0,0);
			$pdf->SetY(-70);
			
			$pdf->SetX(200);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('','');
			$sign=$path."/pdf/images/kannan-sign.png";
			$pdf->Image($sign,$pdf->GetX(),$pdf->GetY()-10,50, 0);
			//$pdf->Cell(0,7,'', 0,1,'L');
			
			$pdf->Cell(0,8,'Prof. Kannan Moudgalya', 0,1,'L');
			$pdf->SetX(194);
			$pdf->Cell(0,7,'Principal Investigator - FOSSEE',0,1,'L');
			$pdf->SetX(190);
			$pdf->Cell(0,7,' Dept. of Chemical Engg., IIT Bombay.', 0,1,'L');
			$pdf->SetX(29);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(0,2,$UniqueString, 0,0,'C');
			$image4=$path."/pdf/images/verify_content.png";
			$pdf->Image($image4, $pdf->GetX(), $pdf->GetY(),20, 0);
			$pdf->SetY(-40);
			$pdf->SetX(80);
			$image3=$path."/pdf/images/iitb.png";
			$image2=$path."/pdf/images/fossee.png";
			
			$pdf->Image($image2, $pdf->GetX()-15, $pdf->GetY()+7,40, 0);
			$pdf->Image($pngAbsoluteFilePath, $pdf->GetX()+50, $pdf->GetY()-5,30, 0);
			$pdf->Image($image3, $pdf->GetX()+110, $pdf->GetY()+3,15, 0);
			
			$pdf->Image($image4, $pdf->GetX()-15, $pdf->GetY()+28,150, 0);
			$pdf->SetFont('Arial','I',8);
			$pdf->SetTextColor(0,0,0);

    			$filename = str_replace(' ','-', $data3->full_name).'-eSim-Textbook-Certificate.pdf';
			$file = $path.'/pdf/temp_certificate/'.$proposal_id.'_'.$filename;
			
			$pdf->Output($file, 'F');

                        header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=" . $filename);   
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Description: File Transfer");            
			header("Content-Length: " . filesize($file));
			flush(); // this doesn't really matter.
			$fp = fopen($file, "r");
			while (!feof($fp))
			{
    				echo fread($fp, 65536);
                               	flush(); // this is essential for large downloads
			} 
		     	fclose($fp); 
			unlink($file);
                        drupal_goto('certificate');
                        return;
		/*}
		else
		{
			drupal_set_message('Your Book Is Still Under Review.', 'status');
		}*/
}	


	function generateRandomString($length = 5) {
   		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
   		$charactersLength = strlen($characters);
   		$randomString = '';
   		for ($i = 0; $i < $length; $i++) {
       			$randomString .= $characters[rand(0, $charactersLength - 1)];
   		}
   		return $randomString;
		}
?>


