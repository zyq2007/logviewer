<?php
/**
 * Jednoduchy autolodaer namespace trid.
 *
 * @author Lex Viatkin <lex@wikidi.com>
 */
class AutoLoader {

	/**
	 * Nahraje pozadovanou tridu
	 *
	 * @see http://groups.google.com/group/php-standards/web/psr-0-final-proposal?pli=1
	 * @param string $className Nazev tridy
	 */
	public static function load($className) {
		$className = ltrim($className, '\\');
		$fileName = '';
		if ($lastNsPos = strripos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		require_once $fileName;
	}
}