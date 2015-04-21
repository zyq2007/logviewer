<?php
namespace logviewer;
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
class View extends \Slim\View {

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

	/**
	 * @param string|array $slug
	 * @param array $query
	 * @return \Url
	 */
	public static function url($slug = '', $query = []) {
		$url = \Url::current();
		$path = is_array($slug) ? implode('/', $slug) : $slug;
		$url->path(Config::dir() . '/' . rtrim($path));
		$url->query($query);
		return $url;
	}
}