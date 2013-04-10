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

	public static function groupLogs($logs, $stripPaths, $removeDirParts = array('logs', 'htdocs')) {
		$grouped = array();

		$quote = function($str) {
			return preg_quote($str, '/');
		};

		$replace = array(
			'/^(' . implode('|', array_map($quote, $stripPaths)) . ')/' => '',
			'/(' . implode('|', array_map($quote, $removeDirParts)) . ')/' => '',
			'/^' . $quote(DIRECTORY_SEPARATOR) . '/' => '',
			'/' . $quote(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) . '?/' => ' – ',
		);

		foreach ($logs as $log) {
			if (!is_readable($log)) continue; // preskocim to co neprectu
			$size = filesize($log);
			$log = preg_replace('/\.log.*\.gz$/', '.log', $log); // soubory z logrotate se zobrazí jako jeden odkaz
			$path = preg_replace(array_keys($replace), array_values($replace), dirname($log));
			$filename = basename($log);
			if (isset($grouped[$path][$filename])) {
				$size += $grouped[$path][$filename]['intsize'];
			}
			$grouped[$path][$filename] = array(
				'path' => $log,
				'file' => $filename,
				'size' => self::formatFileSize($size),
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
}
