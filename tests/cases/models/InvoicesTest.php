<?php

namespace li3_freshbooks\test\cases\models;

use \li3_freshbooks\models\Invoices;

class InvoicesTest extends \lithium\test\Unit {

	public function testWhetherDbQueryWorks() {
	    $id = Invoices::find('first')->id;
	    $this->assertTrue(!empty($id), 'Query did not pull data');
	}
}

?>