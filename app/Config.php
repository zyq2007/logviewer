<?php
namespace logviewer;
/**
 * @method static array dir()
 * @method static array filelist()
 * @method static array highlights()
 * @method static array isMulti()
 * @method static array replace()
 */
class Config extends \stdClass {

	/** @var array */
	private static $data;

	/**
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 */
	public static function __callStatic($name, $arguments) {
		if (static::$data === null) {
			static::$data = require_once __DIR__ . '/../conf/config.php';
		}
		return array_key_exists($name, static::$data) ? static::$data[$name] : null;
	}

}
