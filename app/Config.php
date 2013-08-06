<?php
namespace logviewer;
/**
 * Config trida pro logviewer
 *
 * @property array $_filelist
 * @property array $_isMulti
 * @property array $_replace
 *
 * @property int $merge
 * @property int $lines
 * @property bool $reverse
 * @property bool $tail
 * @property bool $multi
 * @property bool $showmachine
 * @property bool $header
 * @property bool $raw
 *
 *
 * @author Roman Ozana <ozana@omdesign.cz>
 * @author Tomas Ovesny <tomas@wikidi.com>
 */
class Config {

	/** @var array|mixed */
	private $config = array();

	/**
	 * @param string $conf
	 * @throws \Exception
	 */
	public function __construct($conf) {
		if (is_file($conf)) $this->config = include $conf;
	}

	/**
	 * Vrati polozku
	 */
	public function __get($key) {
		if ($key[0] === '_') {
			$key = substr($key, 1); // smazu _ na zacatku
			$group = 'private';
		} else {
			$group = 'public';
		}
		return isset($this->config[$group][$key]) ? $this->config[$group][$key] : null;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key, $value) {
		if ($key[0] === '_') {
			$key = substr($key, 1); // smazu _ na zacatku
			$group = 'private';
		} else {
			$group = 'public';
		}
		$this->config[$group][$key] = $value;
	}

}
