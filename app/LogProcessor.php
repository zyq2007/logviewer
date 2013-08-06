<?php
namespace logviewer;
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
class LogProcessor {

	/** @var  Config */
	private $config;

	/**
	 * @param Config $config
	 */
	public function __construct(Config $config) {
		$this->config = $config;
	}

	public function highlight($log) {
		$log = htmlspecialchars($log, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
		return preg_replace(array_keys($this->config->_replace), array_values($this->config->_replace), $log);
	}

	public static function replaceKeywords($log, $url, $dirname) {
		$replace = array(
			'/\{dirname\}/i' => $dirname,
			'/\{url\}/i' => $url,
		);
		return preg_replace(array_keys($replace), array_values($replace), $log);
	}


}