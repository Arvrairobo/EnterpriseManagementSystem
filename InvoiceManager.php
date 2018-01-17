<?php
include_once 'DBManager.php';
class InvoiceManager { 
	public function getInvoiceId() {
		$db = new DBManager();
		$sql = "SELECT * from ems_invoice_auto_id";
		$result = $db->getARecord($sql);
        return $result;
	}
    // updateNatioanlId
    public function updateNatioanlId($current_national_id) {
        $db = new DBManager();
        $sql = "UPDATE ems_invoice_auto_id set current_india_based_id='$current_national_id'";
        $result = $db->execute($sql);
        return $result;
    }
    public function updateExportlId($current_export_id) {
        $db = new DBManager();
        $sql = "UPDATE ems_invoice_auto_id set current_export_id='$current_export_id'";
        $result = $db->execute($sql);
        return $result;
    }
	public function getAutoIncrimentIDInvoice() {
        $db = new DBManager();
        $sql = "SELECT `AUTO_INCREMENT`
            FROM  INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = 'mukesh'
            AND   TABLE_NAME   = 'ems_invoices'";
        $data = $db->getARecord($sql);
        return $data;
 
    }
    public function saveInvoice($invoice_id, $client_id, $client_name, $client_email, $client_address, $client_gstin, $client_state, $mode_of_invoice, $reverse_charge, $bank_account, $currency_type, $net_amount, $invoice_date) {
    	$db = new DBManager();
    	$sql = "INSERT into ems_invoices(invoice_id, client_id, name, email, address, gstin, state, invoice_mode, reverse_charge, bank_id, currency_type, net_amount, invoice_date) values ('$invoice_id', '$client_id', '$client_name', '$client_email,','$client_address', '$client_gstin', '$client_state' ,'$mode_of_invoice', '$reverse_charge', '$bank_account', '$currency_type', '$net_amount' , '$invoice_date')";
    	//echo $sql;
    	//die();
    	$result = $db->execute($sql);
    	return $result;

    }
    public function saveInvoiceAmount($invoice_id, $desc_of_service, $sac_code, $quantity, $price, $cgst, $sgst, $igst) {
    	$db = new DBManager();
    	$sql = "INSERT into ems_invoice_amount(invoice_id, desc_of_service, sac_code, quantity, price, cgst, sgst, igst) values ('$invoice_id', '$desc_of_service', '$sac_code', '$quantity', '$price', '$cgst', '$sgst', '$igst')";
    	//echo $sql;
    	//die();
    	$result = $db->execute($sql);
    	return $result;

    }
    // preview Invoice
    public function previewInvoice($invoice_id, $client_id, $client_name, $client_address, $client_gstin, $client_state, $mode_of_invoice, $reverse_charge, $bank_account, $currency_type, $net_amount, $invoice_date) {
        $db = new DBManager();
        $sql = "DELETE from ems_invoices_preview";
        $result = $db->execute($sql);
        $sql = "INSERT into ems_invoices_preview(invoice_id, client_id, name, address, gstin, state, invoice_mode, reverse_charge, bank_id, currency_type, net_amount, invoice_date) values ('$invoice_id', '$client_id', '$client_name', '$client_address', '$client_gstin', '$client_state' ,'$mode_of_invoice', '$reverse_charge', '$bank_account', '$currency_type', '$net_amount' , '$invoice_date')";
        //echo $sql;
        //die();
        $result = $db->execute($sql);
        return $result;

    }
    public function previewInvoiceAmount($invoice_id, $desc_of_service, $sac_code, $quantity, $price, $cgst, $sgst, $igst) {
        $db = new DBManager();
        $sql = "DELETE from ems_invoice_amount_preview";
        $result = $db->execute($sql);
        $sql = "INSERT into ems_invoice_amount_preview(invoice_id, desc_of_service, sac_code, quantity, price, cgst, sgst, igst) values ('$invoice_id', '$desc_of_service', '$sac_code', '$quantity', '$price', '$cgst', '$sgst', '$igst')";
        //echo $sql;
        //die();
        $result = $db->execute($sql);
        return $result;

    }
    public function getPreviewInvoiceDetails() {
        $db = new DBManager();
        $sql = "SELECT * from ems_invoices_preview";
        $result = $db->getARecord($sql);
        return $result;
    }
    public function getPreviewServices($invoice_id) {
        $db = new DBManager();
        $sql = "SELECT * from ems_invoice_amount_preview where invoice_id = '$invoice_id'";
        $data = $db->getAllRecords($sql);
        $total = $db->getNumRow($sql);
        while ($row = $db->getNextRow()) {
            $this->desc_of_service[] = $row['desc_of_service'];
            $this->sac_code[] = $row['sac_code'];
            $this->quantity[] = $row['quantity'];
            $this->price[] = $row['price'];
            $this->sgst[] = $row['sgst'];
            $this->cgst[] = $row['cgst'];
            $this->igst[] = $row['igst'];
        }
        return $total;
    }
    //end

    public function getInvoiceDetails($invoice_id) {
        $db = new DBManager();
        $sql = "SELECT * from ems_invoices where invoice_id= '$invoice_id'";
        $result = $db->getARecord($sql);
        return $result;
    }

    public function getServices($invoice_id) {
        $db = new DBManager();
        $sql = "SELECT * from ems_invoice_amount where invoice_id = '$invoice_id'";
        $data = $db->getAllRecords($sql);
        $total = $db->getNumRow($sql);
        while ($row = $db->getNextRow()) {
            $this->desc_of_service[] = $row['desc_of_service'];
            $this->sac_code[] = $row['sac_code'];
            $this->quantity[] = $row['quantity'];
            $this->price[] = $row['price'];
            $this->sgst[] = $row['sgst'];
            $this->cgst[] = $row['cgst'];
            $this->igst[] = $row['igst'];
        }
        return $total;
    }
     
    public function listInvoices()
    {
        $db = new DBManager();
        $sql = "SELECT * from ems_invoices order by id desc";
        $data = $db->getAllRecords($sql);
        $total = $db->getNumRow($sql);
        while ($row = $db->getNextRow()) {
            $this->invoice_id[] = $row['invoice_id'];
            $this->client_name[] = $row['name'];
            $this->invoice_date[] = $row['invoice_date'];
            $this->currency_type[] = $row['currency_type'];
            $this->invoice_amount[] = $row['net_amount'];
            $this->status[] = $row['status'];
        }
        return $total;
    } 
    // list received  invoices 
    public function listReceivedInvoices()
    {
        $db = new DBManager();
        $sql = "SELECT * from ems_receive_invoice order by id desc";
        $data = $db->getAllRecords($sql);
        $total = $db->getNumRow($sql);
        while ($row = $db->getNextRow()) {
            $this->id[] = $row['id'];
            $this->invoice_id[] = $row['invoice_id'];
            $this->client_name[] = $row['name'];
            $this->invoice_date[] = $row['invoice_date'];
            $this->currency_type[] = $row['currency_type'];
            $this->invoice_amount[] = $row['invoice_amount'];
            $this->upload_invoice[] = $row['upload_invoice'];
            $this->status[] = $row['status'];
        }
        return $total;
    }
    // list of Description of Services

    public function listDescOfServices($searchKey) {
        $db = new DBManager();
        $sql = "SELECT DISTINCT desc_of_service from ems_invoice_amount where desc_of_service LIKE '$searchKey%'";
        $data = $db->getAllRecords($sql);
        $arrayNameList =array();
        $arrayName = array();
        while ($row = $db->getNextRow()) {
            $arrayName['value'] = $row['desc_of_service'];
            $arrayName['label'] = $row['desc_of_service'];
            array_push($arrayNameList, $arrayName); 
        }
        return $arrayNameList;
    }

    // receive invoice function
    public function saveReceiveInvoice($invoice_id, $name, $address, $email, $contact_no, $invoice_date, $currency_type, $invoice_amount, $gstin, $upload_invoice ) {
        $db = new DBManager();
        $sql = "INSERT into ems_receive_invoice(invoice_id, name, address, email, contact_no, invoice_date, currency_type, invoice_amount, gstin, upload_invoice) values ('$invoice_id', '$name', '$address', '$email', '$contact_no', '$invoice_date', '$currency_type' ,'$invoice_amount', '$gstin', '$upload_invoice')";
        $result = $db->execute($sql);
        return $result;
    }
    public function saveReceiveInvoiceAmount($invoice_id, $desc_of_service, $sac_code, $quantity, $price, $cgst, $sgst, $igst) {
        $db = new DBManager();
        $sql = "INSERT into ems_invoice_receive_amount(invoice_id, desc_of_service, sac_code, quantity, price, cgst, sgst, igst) values ('$invoice_id', '$desc_of_service', '$sac_code', '$quantity', '$price', '$cgst', '$sgst', '$igst')";
        //echo $sql;
        //die();
        $result = $db->execute($sql);
        return $result;

    }
    // list seller name 
    public function listSellerName($searchKey) {
        $db = new DBManager();
        $sql = "SELECT * from ems_receive_invoice where name LIKE '$searchKey%' and created_at = (SELECT MAX(created_at) from ems_receive_invoice where name LIKE '$searchKey%')";
        $result = $db->execute($sql);       
        $data = $db->getAllRecords($sql);
        $arrayNameList =array();
        $arrayName = array();
        while ($row = $db->getNextRow()) {
            $arrayName['id'] = $row['id'];
            $arrayName['value'] = $row['name'];
            $arrayName['label'] = $row['name'];
            $arrayName['address'] = $row['address'];
            $arrayName['email'] = $row['email'];
            $arrayName['contact_no'] = $row['contact_no'];
            $arrayName['gstin'] = $row['gstin'];
            array_push($arrayNameList, $arrayName); 
        }
        return $arrayNameList;
    }
    public function changeStatus($invoice_id, $status) {
        $db = new DBManager();
        $sql = "UPDATE ems_invoices set status='$status' where invoice_id='$invoice_id'";
        $result = $db->execute($sql);
        return $result;
    }
    // list of Received Description of Services

    public function listDescOfReceivedServices($searchKey) {
        $db = new DBManager();
        $sql = "SELECT DISTINCT desc_of_service from ems_invoice_receive_amount where desc_of_service LIKE '$searchKey%'";
        $data = $db->getAllRecords($sql);
        $arrayNameList =array();
        $arrayName = array();
        while ($row = $db->getNextRow()) {
            $arrayName['value'] = $row['desc_of_service'];
            $arrayName['label'] = $row['desc_of_service'];
            array_push($arrayNameList, $arrayName); 
        }
        return $arrayNameList;
    }
    // check_already_exist_value
    public function check_already_exist_value($field_value, $field_name) {
        $db = new DBManager();
        $sql = "SELECT * from ems_receive_invoice where invoice_id = '$field_value'";
        $total = $db->getNumRow($sql);
        return $total;
    }
}