<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
$current_date = time();
include_once 'DBManager.php';
$DBManager = new DBManager();
include_once 'AppointmentManager.php';
$appointmentManager = new AppointmentManager();
include_once 'validate.php';
if (isset($_POST["saveProbationerAppointment"])) {
  
    $employee_id = mysqli_real_escape_string($DBManager->conn, $_POST['employee_id']);
    $appointment_details = mysqli_real_escape_string($DBManager->conn, $_POST['appointment_details']);
    
	$pdf_name = 'PROB'.$employee_id.'.pdf';
	$target_dir = "uploads/appointment_pdf/probationer/";
	$target_file = $target_dir . $pdf_name;

	if (file_exists($target_file)) {
		unlink($target_file);
	}
	$total = $appointmentManager->checkProbationAppointmentExists($employee_id);
	if(is_array($total)) {
		$result = $appointmentManager->updateProbationerAppointment($employee_id, $appointment_details);
	}
	else {
		$result = $appointmentManager->saveProbationerAppointment($employee_id, $appointment_details, $pdf_name);
	}
		
	if($result) {
		$pdf=exec('/usr/local/bin/wkhtmltopdf --page-size A4 --print-media-type --include-in-outline  http://www.enterhelix.com/mukesh/ems/generatePrabationerAppointment.php?employee_id='.$employee_id.' ../ems/'.$target_file.' 2>&1');
		$_SESSION['probationer_success'] = 'success';
		header("Location: probationerAppointment.php");
	}
}
if (isset($_POST["savePermanentAppointment"])) {
  
    $employee_id = mysqli_real_escape_string($DBManager->conn, $_POST['employee_id']);
    $appointment_details = mysqli_real_escape_string($DBManager->conn, $_POST['appointment_details']);

	$pdf_name = 'PERMA'.$employee_id.'.pdf';
	$target_dir = "uploads/appointment_pdf/permanent/";
	$target_file = $target_dir . $pdf_name;

	if (file_exists($target_file)) {
		unlink($target_file);
	}
	$total = $appointmentManager->checkPermanentAppointmentExists($employee_id);
	if(is_array($total)) {
		$result = $appointmentManager->updatePermanentAppointment($employee_id, $appointment_details);
	}
	else {
		$result = $appointmentManager->savePermanentAppointment($employee_id, $appointment_details, $pdf_name);
	}
	if($result) {
		$pdf=exec('/usr/local/bin/wkhtmltopdf --page-size A4 --print-media-type --include-in-outline  http://www.enterhelix.com/mukesh/ems/generatePermanentAppointment.php?employee_id='.$employee_id.' ../ems/'.$target_file.' 2>&1');
		$_SESSION['permanent_success'] = 'success';
		header("Location: permanentAppointment.php");
	}
	
}
if (isset($_POST["probationer_employee_id"])) {
	$probationer_employee_id = mysqli_real_escape_string($DBManager->conn, $_POST['probationer_employee_id']);
	// echo $probationer_employee_id;
	// die();
	$total = $appointmentManager->checkProbationAppointmentExists($probationer_employee_id);
	if($total == 0) {
		echo $total;
	} else {
		foreach ($total as $key => $value) {
			echo $value;
		}
	}
}
if (isset($_POST["permanent_employee_id"])) {
	$permanent_employee_id = mysqli_real_escape_string($DBManager->conn, $_POST['permanent_employee_id']);
	// echo $permanent_employee_id;
	// die();
	$total = $appointmentManager->checkPermanentAppointmentExists($permanent_employee_id);
	if($total == 0) {
		echo $total;
	} else {
		foreach ($total as $key => $value) {
			echo $value;
		}
	}
}
if (isset($_POST["saveExperienceCertificate"])) {
  
    $employee_id = mysqli_real_escape_string($DBManager->conn, $_POST['employee_id']);
    $experience_details = mysqli_real_escape_string($DBManager->conn, $_POST['experience_details']);

	$pdf_name = 'EXPCERT'.$employee_id.'.pdf';
	$target_dir = "uploads/experience_certificate_pdf/";
	$target_file = $target_dir . $pdf_name;

	if (file_exists($target_file)) {
		unlink($target_file);
	}
	$total = $appointmentManager->checkExperienceCertificateExists($employee_id);
	if(is_array($total)) {
		$result = $appointmentManager->updateExperienceCertificate($employee_id, $experience_details);
	}
	else {
		$result = $appointmentManager->saveExperienceCertificate($employee_id, $experience_details, $pdf_name);
	}
	if($result) {
		$pdf=exec('/usr/local/bin/wkhtmltopdf --page-size A4 --print-media-type --include-in-outline  http://www.enterhelix.com/mukesh/ems/generateExperienceCertificate.php?employee_id='.$employee_id.' ../ems/'.$target_file.' 2>&1');
		$_SESSION['experience_certificate_success'] = 'success';
		header("Location: experienceCertificate.php");
	}
	
}
if (isset($_POST["experience_employee_id"])) {
	$experience_employee_id = mysqli_real_escape_string($DBManager->conn, $_POST['experience_employee_id']);
	// echo $permanent_employee_id;
	// die();
	$total = $appointmentManager->checkExperienceCertificateExists($experience_employee_id);
	if($total == 0) {
		echo $total;
	} else {
		foreach ($total as $key => $value) {
			echo $value;
		}
	}
}