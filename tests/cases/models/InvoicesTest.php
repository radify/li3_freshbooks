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
		/*
		$data = array(
'HTTP/1.1 200 OK',
'Header: Value',
'Connection: close',
'Content-Type: application/xml;charset=UTF-8',
'',
'<?xml version="1.0"?>
<response xmlns="http://www.freshbooks.com/api/" status="ok">
  <invoice>
    <invoice_id>344</invoice_id>
    <client_id>3</client_id>
    <contacts>
        <contact>
            <contact_id>0</contact_id>
        </contact>
    </contacts>
	...
	...
'
);
		*/
		$data = '';
		$this->socket->returnRead = implode("\r\n", $data);

		$result = Invoices::find('first')->id;
	    $this->assertTrue($id, 'Query did not pull data.');
	}
}

?>