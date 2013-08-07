<?php
namespace logviewer;
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
class View extends \Slim\View {

	/** @var stdClass */
	private $config;

	function __construct(\stdClass $config) {
		parent::__construct();
		$this->config = $config;
		$this->data->set('config', $config);
	}

	public function getHeader() {
		require_once __DIR__ . '/templates/header.phtml';
	}

	public function getFilters() {
		$params = $this->params;
		extract($params);
		$get = array_key_exists('log', $_GET) ? array('log' => $_GET['log']) : array();
		require_once __DIR__ . '/templates/filters.phtml';
	}

	public function getFooter() {
		require_once __DIR__ . '/templates/footer.phtml';
	}

	public function __set($name, $value) {
		$this->data->set($name, $value);
	}

	public function __get($name) {
		return $this->data->get($name, null);
	}

	public function getTemplatePathname($file) {
		return __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
	}

	public function url($params = '', $get = array()) {
		if (is_array($params)) {
			$params = array_filter($params);
			return static::getCurrentUrl() . implode('/', $params) . ($get ? '?' . http_build_query($get) : null);
		}
		return static::getCurrentUrl() . $params;
	}

	public function  getCurrentUrl($path = '') {
		if (isset($_SERVER['REQUEST_URI'])) {
			$parse = parse_url(
				(isset($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'off') ? 'https://' : 'http://') .
				(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '')) . $this->config->dir
			);
			$parse['port'] = $_SERVER["SERVER_PORT"]; // setup protocol for sure (80 is default)
			return http_build_url('', $parse);
		}
	}

}