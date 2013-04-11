<?php
namespace logviewer;
/**
 * Config trida pro logviewer
 *
 * @property array $_stripPaths
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
	 */
	public function __construct($conf) {
		if (is_file($conf)) {
			$this->config = include $conf;

			if (empty($_GET)) return; // nebudu menit nic

			// projdu vsechny public a zmenim jejich nastaveni
			foreach ($this->getPublic() as $key => $value) {
				if (is_bool($value)) {
					$this->{$key} = isset($_GET[$key]) ? (bool)$_GET[$key] : false;
					continue;
				}
				$this->{$key} = isset($_GET[$key]) ? $_GET[$key] : null;
			}
		} else {
			throw new \Exception('Config file cannot be found');
		}
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

	/**
	 * Filtruje promennou z requestu
	 *
	 * @param mixed $value
	 */
	private function filter($value) {
		return htmlspecialchars($value);
	}

	public function getPrivate() {
		return $this->config['private'];
	}

	public function getPublic() {
		return $this->config['public'];
	}
}
