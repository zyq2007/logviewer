<?php
namespace logviewer;
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
class LogProcessor {

	public function highlight($log) {
		$log = htmlspecialchars($log, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
		return preg_replace(array_keys(Config::replace()), array_values(Config::replace()), $log);
	}

	public static function replaceKeywords($log, $url, $dirname) {
		$replace = array(
			'/\{dirname\}/i' => $dirname,
			'/\{url\}/i' => $url,
		);
		return preg_replace(array_keys($replace), array_values($replace), $log);
	}


}