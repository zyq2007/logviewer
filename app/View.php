<?php
namespace logviewer;
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
class View extends \Slim\View {

	public function __set($name, $value) {
		$this->data->set($name, $value);
	}

	public function __get($name) {
		return $this->data->get($name, null);
	}

	public function getTemplatePathname($file) {
		return __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
	}

}