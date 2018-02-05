<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include('settings/config.php');
date_default_timezone_set('Asia/Kolkata');
$current_date = time();
include_once 'DBManager.php';
include_once 'EmployeeManager.php';
$DBManager = new DBManager();
include_once 'Validate.php';
$validate = new Validate();
include_once 'PayrollManager.php';
$payrollManager = new PayrollManager();
include_once 'AdminManager.php';
$adminManager = new AdminManager();
$companyInfo = $adminManager->getAdminDetails();
// save payroll
if (isset($_POST["savePayroll"])) {
	foreach( $_POST as $key => $value )
	{	
		$key = 'session_payroll_'.$key;
		if(isset($_SESSION[$key]))	{
			unset($_SESSION[$key]); 
		}
	}
	$employee_id = mysqli_real_escape_string($DBManager->conn, $_POST['employee_id']);
	
	$employee_email = mysqli_real_escape_string($DBManager->conn, $_POST['email']);
	$bankAccount = mysqli_real_escape_string($DBManager->conn, $_POST['bankAccount']);
    $name = mysqli_real_escape_string($DBManager->conn, $_POST['name']);
    $basic = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['basic']));
    if($_POST['house_rent_allowance'] != '') {

    	$house_rent_allowance = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['house_rent_allowance']));
    } else {
    	$house_rent_allowance = 0.00;
    }
    if($_POST['conveyance_allowance'] != '') {
    	$conveyance_allowance = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['conveyance_allowance']));
    } else {
    	$conveyance_allowance = 0.00;
    }
    if($_POST['special_allowance'] != '') {
    	$special_allowance = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['special_allowance']));
    } else {
    	$special_allowance = 0.00;
    }
    if($_POST['bonus'] != '') {
    	$bonus = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['bonus']));
    } else {
    	$bonus= 0.00;
    }
    if($_POST['overtime'] != '') {
    	$overtime = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['overtime']));
    } else {
    	$overtime = 0.00;
    }
    if($_POST['overtimeAmount'] != '') {
    	$overtimeAmount = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['overtimeAmount']));
    } else {
    	$overtimeAmount = 0.00;
    }
    if($_POST['professional_tax'] != '') {
    	$professional_tax = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['professional_tax']));
    } else {
    	$professional_tax = 0.00;
    }
    if($_POST['income_tax'] != '') {
    	$income_tax = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['income_tax']));
    } else {
    	$income_tax = 0.00;
    }
    if($_POST['provident_fund'] != '') {
    	$provident_fund = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['provident_fund']));
    } else {
    	$provident_fund = 0.00;
    }
    if($_POST['health_insurance'] != '') {
    	$health_insurance = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['health_insurance']));
    } else {
    	$health_insurance = 0.00;
    }
    if($_POST['un_paid_days_amount'] != '') {
    	$un_paid_days_amount = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['un_paid_days_amount']));
    } else {
    	$un_paid_days_amount = 0.00;
    } 
    if($_POST['misc'] != '') {
    	$misc = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['misc']));
    } else {
    	$misc = 0.00;
    }
    if($_POST['gross_earnings'] != '') {
    	$gross_earnings = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['gross_earnings']));
    } else {
    	$gross_earnings = 0.00;
    }
    if($_POST['gross_deductions'] != '') {
    	$gross_deductions = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['gross_deductions']));
    } else {
    	$gross_deductions = 0.00;
    }
    if($_POST['net_pay'] != '') {
		$net_pay = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['net_pay']));

	} else {
		$net_pay = 0.00;
	}
	$paid_days_count = mysqli_real_escape_string($DBManager->conn,$_POST['paid_days_count']);
	if($_POST['un_paid_days_count'] != '') { 
		$un_paid_days_count = mysqli_real_escape_string($DBManager->conn,$_POST['un_paid_days_count']);
	} else {
		$un_paid_days_count = 0.00;
	}
	$status = mysqli_real_escape_string($DBManager->conn,$_POST['status']);	
	$name_clean=preg_replace('/\s+/', '', $name);
	$pdf_name = $employee_id.date("dmY",$current_date).'.pdf';
	$target_dir = "uploads/payroll_pdf/";
	$target_file = $target_dir . $pdf_name;
	if (file_exists($target_file)) {
		$_SESSION['payroll_error'] = 'Payroll already generated for the employee for this month.';

		foreach( $_POST as $key => $value )
		{
			$key = 'session_payroll_'.$key;
		   $_SESSION[$key] = $value;
		   //If you need to database process the data, you can put mysql_escape_string( $value );
		}
		header ("Location: payroll");
	}
	else {
		$result = $payrollManager->savePayroll($employee_id, $basic, $house_rent_allowance, $conveyance_allowance, $special_allowance, $bonus, $overtime, $overtimeAmount, $professional_tax, $income_tax, $provident_fund, $health_insurance, $un_paid_days_amount, $misc, $gross_earnings, $gross_deductions, $net_pay, $pdf_name, $paid_days_count, $un_paid_days_count, $status);
		if($result) {
			$generatePdfUrl = $absoluteUrl.'generatePayrollPdf.php?employee_id='.$employee_id;

			$pdf=exec('/usr/local/bin/wkhtmltopdf --page-size A4 --print-media-type --include-in-outline '.$generatePdfUrl.' '.$target_file.' 2>&1');
			if(!$pdf) {
				$_SESSION['payroll_error'] = 'Failed to generate Pdf.';
				header('location:payroll');
				die();
			}
			// send mail to employee for salary credit acknowledgement
			if($sendEmailToEmployee == true) { 
				// create email header
				$paysilipHeaders  = 'MIME-Version: 1.0' . "\r\n";
				$paysilipHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

				$paysilipHeaders .= 'From: '.$fromEMS."\r\n".'Reply-To: '.$fromEMS."\r\n" .'X-Mailer: PHP/' . phpversion();
			    // end header
				$lastMonth = date("F Y",strtotime("-1 month"));
				$pdf_link = $absoluteUrl.$target_file;
				$paysilipMessage = '<html><body>';

				$paysilipMessage .= '<p>Dear '.$name.',</p>';

				$paysilipMessage .= '<p>Salary for the month of '.$lastMonth.' has been credited (will be credited) to your Bank A/c # ' .$bankAccount.'</p>';
				$paysilipMessage .='<p>Click the link below for the Payslip.</p>';
				$paysilipMessage .= $pdf_link;
				$paysilipMessage .= '<p>Thanks<br>'.$companyInfo['company_name'].'</p>';
				$paysilipMessage .= '</body></html>';
				mail($employee_email, $payslipSubject, $paysilipMessage, $paysilipHeaders);
			}
			// mail send end

			if($_POST["savePayroll"] == 'Print') {
				//echo $_POST["savePayroll"];
				$_SESSION['payroll_success'] = 'Congrats, Payroll generated successfully.';
				header('Location:generatePayrollPdf.php?print_payroll='.$employee_id);
				die();
			} else {
				$_SESSION['payroll_success'] = 'Congrats, Payroll generated successfully.';
				header("Location:payroll");
				die();
			}
		}
	}
}

// preview payroll
if (isset($_POST["previewPayroll"])) {
	$employee_id = mysqli_real_escape_string($DBManager->conn, $_POST['employee_id']);
	$email = mysqli_real_escape_string($DBManager->conn, $_POST['email']);
    $name = mysqli_real_escape_string($DBManager->conn, $_POST['name']);
    $basic = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['basic']));
    if($_POST['house_rent_allowance'] != '') {

    	$house_rent_allowance = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['house_rent_allowance']));
    } else {
    	$house_rent_allowance = 0.00;
    }
    if($_POST['conveyance_allowance'] != '') {
    	$conveyance_allowance = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['conveyance_allowance']));
    } else {
    	$conveyance_allowance = 0.00;
    }
    if($_POST['special_allowance'] != '') {
    	$special_allowance = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['special_allowance']));
    } else {
    	$special_allowance = 0.00;
    }
    if($_POST['bonus'] != '') {
    	$bonus = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['bonus']));
    } else {
    	$bonus= 0.00;
    }
    if($_POST['overtime'] != '') {
    	$overtime = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['overtime']));
    } else {
    	$overtime = 0.00;
    }
    if($_POST['overtimeAmount'] != '') {
    	$overtimeAmount = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['overtimeAmount']));
    } else {
    	$overtimeAmount = 0.00;
    }
    if($_POST['professional_tax'] != '') {
    	$professional_tax = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['professional_tax']));
    } else {
    	$professional_tax = 0.00;
    }
    if($_POST['income_tax'] != '') {
    	$income_tax = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['income_tax']));
    } else {
    	$income_tax = 0.00;
    }
    if($_POST['provident_fund'] != '') {
    	$provident_fund = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['provident_fund']));
    } else {
    	$provident_fund = 0.00;
    }
    if($_POST['health_insurance'] != '') {
    	$health_insurance = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['health_insurance']));
    } else {
    	$health_insurance = 0.00;
    }
    if($_POST['un_paid_days_amount'] != '') {
    	$un_paid_days_amount = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['un_paid_days_amount']));
    } else {
    	$un_paid_days_amount = 0.00;
    } 
    if($_POST['misc'] != '') {
    	$misc = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['misc']));
    } else {
    	$misc = 0.00;
    }
    if($_POST['gross_earnings'] != '') {
    	$gross_earnings = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['gross_earnings']));
    } else {
    	$gross_earnings = 0.00;
    }
    if($_POST['gross_deductions'] != '') {
    	$gross_deductions = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['gross_deductions']));
    } else {
    	$gross_deductions = 0.00;
    }
    if($_POST['net_pay'] != '') {
		$net_pay = mysqli_real_escape_string($DBManager->conn, $validate->removeComma($_POST['net_pay']));

	} else {
		$net_pay = 0.00;
	}
	$paid_days_count = mysqli_real_escape_string($DBManager->conn,$_POST['paid_days_count']);
	if($_POST['un_paid_days_count'] != '') { 
		$un_paid_days_count = mysqli_real_escape_string($DBManager->conn,$_POST['un_paid_days_count']);
	} else {
		$un_paid_days_count = 0.00;
	}
	$status = mysqli_real_escape_string($DBManager->conn,$_POST['status']);	
	$pdf_name = $employee_id.date("dmY",$current_date).'.pdf';
	$result = $payrollManager->previewPayroll($employee_id, $basic, $house_rent_allowance, $conveyance_allowance, $special_allowance, $bonus, $overtime, $overtimeAmount, $professional_tax, $income_tax, $provident_fund, $health_insurance, $un_paid_days_amount, $misc, $gross_earnings, $gross_deductions, $net_pay, $pdf_name, $paid_days_count, $un_paid_days_count, $status);
	if($result) {
		echo "success";
	} else {
		echo "fail";
	}
}
//check_payroll_already_generated
if(isset($_POST['check_payroll_generated_id'])) {
	$currentMonth = date("Y-m",$current_date);
	$employee_id = $status = mysqli_real_escape_string($DBManager->conn,$_POST['check_payroll_generated_id']);	
	$result= $payrollManager->checkPayrollAlreadyGenerated($employee_id, $currentMonth);
	if($result) {
		echo $result;
	}
}
?>
