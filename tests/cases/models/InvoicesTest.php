<?php

namespace li3_freshbooks\tests\cases\models;

use lithium\data\Connections;
use li3_freshbooks\extensions\adapter\data\source\http\Freshbooks;
use li3_freshbooks\tests\mocks\extensions\data\source\http\MockFreshbooksSocket;
use li3_freshbooks\models\Invoices;

class InvoicesTest extends \lithium\test\Unit {

	public $source;

	public $socket;

	protected $_backup = array();

	public function setUp() {
		$this->_backup['connections'] = Connections::config();
		Connections::reset();

		$this->socket = new MockFreshbooksSocket();
		$this->source = new Freshbooks(array(
			'login' => 'test',
			'password' => '123',
			'socket' => $this->socket
		));

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
		$response = array(
			'HTTP/1.1 200 OK',
			'Header: Value',
			'Connection: close',
			'Content-Type: application/xml;charset=UTF-8',
			'',
			'<?xml version="1.0"?>
		       <response xmlns="http://www.freshbooks.com/api/" status="ok">
		  	     <invoice>
		    	    <invoice_id>1</invoice_id>
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

		$data = $response;
		$this->socket->returnRead = implode("\r\n", $data);

		$result = Invoices::find('all');
		$this->assertTrue($result, 'Query did not pull data.');
	}
}

?>