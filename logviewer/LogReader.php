<?php
namespace logviewer;
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 * @author Jan Prachař <jan.prachar@gmail.com>
 * @author Tomas Ovesny <tomas@wikidi.com>
 */
class LogReader {
	/** @var \logviewer\Config */
	private $config;
	/** @var string */
	private $log = '';
	/** @var string */
	private $logType = '';

	public function __construct(Config $config, $log, $logType) {
		$this->config = $config;
		$this->log = $log;
		$this->logType = $logType;
	}

	public function display() {
		switch ($this->logType) {
			case 'png':
			case 'jpeg':
			case 'jpg':
			case 'html':
				return file_get_contents($this->log);
			case 'txt':
			case 'log':
			default:
				if ($this->config->multi) {
					$output = $this->processRemote();
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
		foreach ($this->config->_filelist as $link) {
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
			throw new NotReadableException('File ' . $this->log  . ' does not match config filelist.');
		}

		$output = array();
		exec('for i in `ls -t -r ' . $glob . '`; do zcat -f $i 2>&1; done | ' . ($this->config->tail ? 'tail' : 'head') . ' -n ' . escapeshellarg($limit), $output, $retval);
		return $output;
	}

	private function formatOutput($output, $reverse, $showmachine) {
		if ($reverse || $showmachine) {
			if ($reverse) $output = array_reverse($output);

			if ($showmachine) $output = array_map(
				function($line) {
					return '-' . $_SERVER['HTTP_HOST'] . '- ' . $line;
				}, $output
			);
		}
		return implode(PHP_EOL, $output);
	}

	private function processLocal($log) {
		$limit = ($this->config->lines > 0) ? $this->config->lines : 150;
		try {
			$output = $this->cat($log, $limit);
		} catch (NotReadableException $e) {
			//@TODO hezčí formátování
			die($e->getMessage());
		}
		return $this->formatOutput($output, $this->config->reverse, $this->config->showmachine);
	}

	private function processRemote() {
		// pripravim si parametry pro remoote
		parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $params);
		$params['raw'] = '1';
		$params['multi'] = '0';

		$protocol = strpos($_SERVER['SERVER_PROTOCOL'], 'HTTPS') === false ? 'http://' : 'https://';
		$port = $_SERVER['SERVER_PORT'];
		$host = parse_url($_SERVER['HTTP_HOST'], PHP_URL_PORT) ? parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST) : $_SERVER['HTTP_HOST'];
		$script = $_SERVER['SCRIPT_NAME'];

		// pro testovani
		//$host = 'wikidi-admin.1.web.srv.wikidi.net'; $port = '8080'; $params['log'] = '/var/www/wikidi/logs/php/error.log';

		$url = $protocol . $host . ':' . $port . $script . '?' . http_build_query($params);

		if (preg_match($this->config->_isMulti, $url)) {
			foreach ($this->getMultiUrl($url) as $remoote) {
				$output[parse_url($remoote, PHP_URL_HOST)] = $this->file_get_contents($remoote);
			}
		} else {
			return $this->processLocal($this->log); // nacist pouze lokalni
		}

		return ($this->config->merge) ? implode(PHP_EOL . str_repeat('=', 80) . PHP_EOL, $output) : $output;
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

		$cUrlHndl = curl_init($url);
		curl_setopt_array($cUrlHndl, $aOptions);
		$binaryContents = curl_exec($cUrlHndl);
		curl_close($cUrlHndl);

		return $binaryContents;
	}
}


class NotReadableException extends \Exception {}
