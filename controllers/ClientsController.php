<?php

namespace li3_freshbooks\controllers;

use li3_freshbooks\models\Clients;

class ClientsController extends \lithium\action\Controller {

	public function index() {
		$result = Clients::find('all');
	}
}

?>