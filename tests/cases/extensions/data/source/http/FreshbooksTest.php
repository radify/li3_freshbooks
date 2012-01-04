<?php

namespace li3_freshbooks\tests\cases\extensions\data\source\http;

use lithium\data\Connections;
use lithium\data\model\Query;
use li3_freshbooks\models\Invoices;
use li3_freshbooks\extensions\adapter\data\source\http\Freshbooks;

class FreshbooksTest extends \lithium\test\Unit {

	public $source;
	
	protected $_models = array(
		'invoices' => 'li3_freshbooks\models\Invoices'
	);

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

	public function testConnection() {
		$result = $this->source->connect();
		$this->assertTrue($result);

		$result = $this->source->disconnect();
		$this->assertTrue($result);
	}

	public function testEnabled() {
		$result = Freshbooks::enabled();
		$this->assertTrue($result);

		$result = Freshbooks::enabled('arrays');
		$this->assertTrue($result);

		$result = Freshbooks::enabled('transactions');
		$this->assertFalse($result);

		$result = Freshbooks::enabled('booleans');
		$this->assertTrue($result);

		$result = Freshbooks::enabled('relationships');
		$this->assertFalse($result);
	}

 	public function testCast() {
		$result = $this->source->cast($this->source, array(''));
		$this->assertTrue($result);
	}
	
	public function testRead() {
		$query = new Query(array('model' => $this->_models['invoices']));
		$results = $this->source->read($query);
		$expected = '';
		$result = $results->first();
		$this->assertTrue($result);
	}
}

?>