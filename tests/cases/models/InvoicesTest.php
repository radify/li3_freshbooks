<?php

namespace li3_freshbooks\tests\cases\models;

use lithium\data\Connections;
use li3_freshbooks\extensions\adapter\data\source\http\Freshbooks;
use li3_freshbooks\models\Invoices;

class InvoicesTest extends \lithium\test\Unit {

	public $source;

	protected $_backup = array();

	protected $_testConfig = array(
		'login' => 'test',
		'password' => '123',
		'socket' => 'li3_freshbooks\tests\mocks\data\source\http\MockFreshbooksSocket'
	);

	public function setUp() {
		$this->_backup['connections'] = Connections::config();

		Connections::reset();
		$this->source = new Freshbooks(array('socket' => false));

		Connections::config(array(
			'freshbooks' => array(
				'object' => &$this->source,
				'adapter' => 'Freshbooks'
			)
		));
	}

	public function tearDown() {
		Connections::reset();
		Connections::config($this->_backup['connections']);
	}

	public function testFind() {
	    $result = Invoices::find('first')->id;
	    $this->assertTrue($id, 'Query did not pull data.');
	}
}

?>