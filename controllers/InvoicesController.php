<?php

namespace li3_freshbooks\controllers;

use li3_freshbooks\models\Invoices;

class InvoicesController extends \lithium\action\Controller {
	
	
	public function index() {
		$result = Invoices::find('all');
	}	
}

?>