<?php
	$server_path 		= dirname(__FILE__);
	$invoice_path 		= str_replace('public_html/apiv2/config','factures/',$server_path);
	$old_invoice_path 	= str_replace('public_html/apiv2/config','old_factures/',$server_path);
	$oldInvoice 		= glob($old_invoice_path.'*.pdf');

	foreach($oldInvoice as $old){
		$InvoiceOld[] = str_replace('/var/www/old_factures/','',$old);
	}
	
	return [
		'INVOICE'			=> $invoice_path,
		'OLD_INVOICE_PATH'	=> $old_invoice_path,
		'OLD_INVOICE'		=> serialize($InvoiceOld)
	]
?>