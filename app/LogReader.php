<?php
namespace logviewer;
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 * @author Jan Prachař <jan.prachar@gmail.com>
 * @author Tomas Ovesny <tomas@wikidi.com>
 */
class LogReader {

	/** @var string */
	public $lines = 150;
	/** @var bool */
	public $direction = true;
	/** @var bool */
	public $read = true;
	/** @var bool */
	public $type = false;
	/** @var string */
	public $log = '';
	/** @var string */
	public $mime = '';

	public function display() {
		switch ($this->mime) {
			case 'png':
			case 'jpeg':
			case 'jpg':
			case 'html':
				return file_exists($this->log) ? file_get_contents($this->log) : 'Missing file : ' . $this->log;
			case 'txt':
			case 'log':
			default:
				if ($this->type) {
					$output = $this->processRemote($this->log);
				} else {
					$output = $this->processLocal($this->log);
				}
				break;
		}

		return $output;
	}

	private function cat($glob, $limit) {
		$phpglob = preg_replace('/\.log$/', '.log{,*.gz}', $glob); //logrotate support
		$glob = preg_replace('/^(.*\.log)$/', '\1 \1*.gz', $glob); //logrotate support

		$matched = false;
		foreach (Config::filelist() as $link) {
			$pattern = '#^' . str_replace('\*', '.+', preg_quote($link, '#')) . '$#i';
			if (preg_match($pattern, $this->log)) {
				$matched = true;
				if (!glob($phpglob, \GLOB_BRACE)) {
					throw new NotReadableException('File ' . $this->log . ' is not accesible or does not exists!');
				}
				break;
			}
		}
		if (!$matched) {
			throw new NotReadableException('File ' . $this->log . ' does not match config filelist.');
		}

		$output = array();
		exec(
			'for i in `ls -t -r ' . $glob . '`; do gzip -dc -f $i 2>&1; done | ' . ($this->read ? 'tail' : 'head') . ' -n ' . escapeshellarg(
				$limit
			), $output, $retval
		);
		return $output;
	}

	private function processLocal($log) {
		$limit = ($this->lines > 0) ? intval($this->lines) : 150;
		try {
			$output = $this->cat($log, $limit);
		} catch (NotReadableException $e) {
			die($e->getMessage());
		}

		$output = ($this->direction) ? array_reverse($output) : $output;
		return implode(PHP_EOL, $output);
	}

	private function processRemote($log) {
		$params = array('raw', $this->lines, $this->read, 'single', $this->direction);
		$get = array_key_exists('log', $_GET) ? array('log' => $_GET['log']) : array();
		$url = View::url($params, $get);

		// url for testing multiple
		//$url = 'http://wikidi-admin.1.web.srv.wikidi.net:8088/logviewer/' . implode('/', $params) . '?log=/var/www/testomato/logs/php/error.log';

		if (preg_match(Config::isMulti(), $url)) {
			$output = array();
			foreach ($this->getMultiUrl($url) as $remote) {
				$output[] = [
					'host' => parse_url($remote, PHP_URL_HOST),
					'url' => strtok($remote, '?'),
					'output' => $this->file_get_contents($remote)
				];
			}
		} else {
			return $this->processLocal($this->log); // nacist pouze lokalni
		}

		return $output;
	}

	private function getMultiUrl($url) {
		$url = urldecode($url);
		$host = preg_replace('/[0-9]+/i', '%s', parse_url($url, PHP_URL_HOST), 1); // prvni cislo nahradim
		$url = preg_replace('/[0-9]+/i', '%s', urldecode($url), 1); // prvni cislo nahradim

		$badDsn = Utils::getDnsTarget(sprintf($host, 255)); // tato DNS neexistuje
		$urls = array();
		for ($i = 1; $i < 255; $i++) {
			$target = Utils::getDnsTarget(sprintf($host, $i)); // je ziv host ?
			if (!$target || $target == $badDsn) break;
			$urls[] = sprintf($url, $i);
		}
		return $urls;
	}

	private function file_get_contents($url) {
		$aOptions = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false
		);

		$curl = curl_init($url);
		curl_setopt_array($curl, $aOptions);
		$binaryContents = curl_exec($curl);
		curl_close($curl);

		return $binaryContents;
	}
}


class NotReadableException extends \Exception {
}
