<?php

namespace li3_freshbooks\extensions\adapter\data\source\http;

use SimpleXml;
use lithium\util\String;
use lithium\util\Inflector;
use lithium\data\model\QueryException;

class Freshbooks extends \lithium\data\source\Http {

	/**
	 * Class dependencies.
	 */
	protected $_classes = array(
		'service' => 'lithium\net\http\Service',
		'entity' => 'lithium\data\entity\Document',
		'set' => 'lithium\data\collection\DocumentSet',
	);

	protected $_schemas = array(
		'clients' => array(
			'name' => 'client',
			'operations' => array('list', 'get', 'update', 'delete', 'create'),
			'schema' => array(
				'client_id'     => array('type' => 'integer'),
				'first_name'    => array('type' => 'string'),
				'last_name'     => array('type' => 'string'),
				'organization'  => array('type' => 'string'),
				'email'         => array('type' => 'string'),
				'username'      => array('type' => 'string'),
				'contacts'      => array('type' => 'object', 'array' => true),
				'work_phone'    => array('type' => 'string'),
				'home_phone'    => array('type' => 'string'),
				'mobile'        => array('type' => 'string'),
				'fax'           => array('type' => 'string'),
				'language'      => array('type' => 'string'),
				'currency_code' => array('type' => 'string'),
				'credits'       => array('type' => 'object', 'array' => true),
				'notes'         => array('type' => 'string'),
				'p_street1'     => array('type' => 'string'),
				'p_street2'     => array('type' => 'string'),
				'p_city'        => array('type' => 'string'),
				'p_state'       => array('type' => 'string'),
				'p_country'     => array('type' => 'string'),
				'p_code'        => array('type' => 'string'),
				's_street1'     => array('type' => 'string'),
				's_street2'     => array('type' => 'string'),
				's_city'        => array('type' => 'string'),
				's_state'       => array('type' => 'string'),
				's_country'     => array('type' => 'string'),
				's_code'        => array('type' => 'string'),
				'links'         => array('type' => 'object', 'array' => true),
				'vat_name'      => array('type' => 'string'),
				'vat_number'    => array('type' => 'string'),
				'updated'       => array('type' => 'date'),
				'folder'        => array('type' => 'string')
			)
		),
		'invoices' => array(
			'name' => 'invoice',
			'operations' => array('list', 'get', 'update', 'delete', 'create'),
			'schema' => array(
				'invoice_id'		 	=> array('type' => 'integer'),
				'estimate_id'		 	=> array('type' => 'integer'),
				'recurring_id' 		 	=> array('type' => 'integer'),
				'status' 		=> array('type' => 'string'),
				'amount'		=> array('type' => 'integer'),
				'amount_outstanding' 			=> array('type' => 'integer'),
				'paid' 			=> array('type' => 'integer'),
				'date'			=> array('type' => 'string'),
				'name'			=> array('type' => 'string'),
				'description'		 	=> array('type' => 'string'),
				'unit_cost'		=> array('type' => 'integer'),
				'quantity'		=> array('type' => 'integer'),
				'tax1_name'		=> array('type' => 'integer'),
				'tax2_name'		=> array('type' => 'integer'),
				'tax1_percent'		 	=> array('type' => 'integer'),
				'tax2_percent'		 	=> array('type' => 'integer'),
				'compound_tax'		 	=> array('type' => 'integer'),
				'time_entry'		 	=> array('type' => 'integer'),
				'time_entries'		 	=> array('type' => 'object', 'array' => true),
				'time_entry_id'		 	=> array('type' => 'integer')
			)
		)
	);

	/**
	 * Constructor.
	 *
	 * @param array $config Configuration options.
	 */
	public function __construct(array $config = array()) {
		$defaults = array(
			'scheme'       => 'https',
			'host'         => '{:login}.freshbooks.com',
			'port'         => null,
			'login'        => null,
			'password'     => '',
			'auth'         => 'Basic',
			'version'      => '1.1',
			'path'         => '/api/2.1'
		);
		$config += $defaults;

		$config['host'] = String::insert($config['host'], $config);
		$config['login'] = $config['password'];
		$config['password'] = 'x';

		parent::__construct($config);
	}

	protected function _init() {
		parent::_init();
		$config = $this->_config + array('headers' => array('Host'));
		unset($config['type']);
		$this->connection = $this->_instance('service', $config);
	}

	public static function enabled($feature = null) {
		if (!$feature) {
			return true;
		}
		$features = array(
			'arrays' => true,
			'transactions' => false,
			'booleans' => true,
			'relationships' => false
		);
		return isset($features[$feature]) ? $features[$feature] : null;
	}

	public function cast($entity, array $data, array $options = array()) {
		foreach($data as $key => $val) {
			if (!is_array($val)) {
				continue;
			}
			$class = 'entity';
			$model = $entity->model();
			$data[$key] = $this->item($model, $val, compact('class'));
		}
		return parent::cast($entity, $data, $options);
	}

	/**
	 * Data source READ operation.
	 *
	 * @param string $query
	 * @param array $options
	 * @return mixed
	 */
	public function read($query, array $options = array()) {
		$params = $query->export($this);
		list($path, $data) = $this->_request($params);
		$source = $params['source'];
		$name = Inflector::singularize($source);

		$data = str_replace("\n", '', $this->_render("$name.list"));
		$result = $this->connection->post('xml-in', $data, array('type' => 'xml'));
		$result = $data = json_decode(json_encode(simplexml_load_string($result)), true);

		return $this->item($query->model(), $result[$source], array('class' => 'set'));
	}

	protected function _request($params) {
		$schema = $this->_schemas[$params['source']];
		$method = "{$schema['name']}.list";
		return array("{$this->_config['path']}", $this->_render($method));
	}

	protected function _render($method, array $data = array()) {
		$result  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$result .= "<request method=\"{$method}\">\n</request>";
		return $result;
	}
}

?>