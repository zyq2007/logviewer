<?php
namespace logviewer;
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 * @author Tomas Ovesny <tomas@wikidi.com>
 * @author Jan Prachař <jan.prachar@gmail.com>
 */
class Utils {

	/**
	 * Vrati vsechny soubory na zaklade pole patternu
	 *
	 * @param array $patterns vzory
	 * @return array $files soubory
	 */
	public static function glob($patterns) {
		$files = array();
		foreach ($patterns as $pattern) {
			$pattern = preg_replace('/\.log$/', '.log{,*.gz}', $pattern); //logrotate support
			$files = array_merge((array)$files, (array)glob($pattern, \GLOB_BRACE));
		}
		return $files;
	}

	public static function prepareLogs($logs) {
		$grouped = array();

		foreach ($logs as $log) {
			if (!is_readable($log)) continue;

			$size = filesize($log);
			$log = preg_replace('/\.log.*\.gz$/', '.log', $log);
			$path = dirname($log);
			$filename = basename($log);

			if (isset($grouped[$path][$filename])) {
				$size += $grouped[$path][$filename]['intsize'];
			}
			$grouped[$path][$filename] = array(
				'path' => $log,
				'file' => $filename,
				'size' => static::formatFileSize($size),
				'intsize' => $size
			);
		}

		return $grouped;
	}

	/**
	 * Zformatuje velikost souboru
	 *
	 * @param int $filesize
	 * @return string $filesize
	 */
	private static function formatFileSize($filesize) {
		if (intval($filesize) > 0) {
			$units = array(' B', ' KB', ' MB', ' GB', ' TB');
			for ($i = 0; $filesize >= 1024 && $i < 4; $i++) {
				$filesize /= 1024;
			}
			$filesize = round($filesize, 2) . $units[$i];
		}
		return $filesize;
	}


	public static function highlights($str, $highlights = array(), $tag = 'span') {
		array_map('preg_quote', $highlights);
		preg_match_all('/(' . implode('|', $highlights) . ')/i', $str, $marches);

		if ($marches = reset($marches)) {
			$marches = array_map('ucfirst', $marches);
			return '<strong>' . implode('</strong> - <strong>', $marches) . '</strong>';
		} else {
			return $str;
		}
	}

	/**
	 * Vrati target ze zaznamu DNS
	 */
	public static function getDnsTarget($host) {
		if (!$re = dns_get_record($host)) {
			return false;
		}
		if (!array_key_exists('target', $re[0])) {
			return $re[0]['ip'];
		}

		return $re[0]['target'];
	}

	/**
	 * Return server name
	 *
	 * @return string
	 */
	public static function getServerName() {
		return parse_url($_SERVER['HTTP_HOST'], PHP_URL_PORT) ? parse_url(
			$_SERVER['HTTP_HOST'], PHP_URL_HOST
		) : $_SERVER['HTTP_HOST'];
	}
}
