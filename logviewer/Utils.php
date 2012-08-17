<?php
namespace logviewer;
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 * @author Tomas Ovesny <tomas@wikidi.com>
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
			$files = array_merge((array)$files, (array)glob($pattern));
		}
		return $files;
	}

	public static function groupLogs($logs, $stripPaths, $removeDirParts = array('logs', 'htdocs')) {
		$grouped = array();

		$qoute = function($str) {
			return preg_quote($str, '/');
		};

		$replace = array(
			'/^(' . implode('|', array_map($qoute, $stripPaths)) . ')/' => '',
			'/(' . implode('|', array_map($qoute, $removeDirParts)) . ')/' => '',
			'/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/' => '',
			'/' . preg_quote(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, '/') . '?/' => ' - ',
		);

		foreach ($logs as $log) {
			if (!is_readable($log)) continue; // preskocim to co neprectu
			$path = preg_replace(array_keys($replace), array_values($replace), dirname($log));
			$grouped[$path][] = array(
				'path' => $log,
				'file' => basename($log),
				'size' => self::formatFileSize(filesize($log))
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