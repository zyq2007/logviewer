<?php
namespace logviewer;
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 * @author Tomas Ovesny <tomas@wikidi.com>
 */
class Logviewer {

	/** @var \logviewer\Config */
	private $config;

	/**
	 * Nacist config
	 *
	 * @param Config $config
	 */
	public function __construct(Config $config) {
		$this->config = $config;
	}

	/**
	 * Vypise seznam souboru
	 */
	public function getList() {
		$view = new View('getlist.phtml');
		$logsList = Utils::glob($this->config->_filelist);
		$view->logs = Utils::groupLogs($logsList, $this->config->_stripPaths);
		$view->showUrl = 'index.php?' . http_build_query($this->config->getPublic());
		$view->filters = $this->getFilters();
		$view->config = $this->config;
		$view->version = $this->config->_version;
		echo $view->render(); // vystup
	}

	/**
	 * Na vystup posle raw data
	 *
	 * @param $content
	 * @param $logType
	 * @param $file
	 */
	private function showRaw($content, $logType, $file) {
		switch ($logType) {
			case 'html':
				header('Content-Type: text/html');
				break;
			case 'jpeg':
			case 'jpg':
				header('Content-Type: image/jpeg');
				break;
			default:
				header('Content-Type: text/plain');
				break;
		}
		header('Content-Type: application/force-download');
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename="' . $file . '"');
		echo is_array($content) ? implode(PHP_EOL, $content) : $content;
	}

	public function show() {
		$log = isset($_GET['log']) ? htmlspecialchars($_GET['log']) : null;
		$logReader = new LogReader($this->config, $log, $logType = pathinfo($log, PATHINFO_EXTENSION));

		if ($this->config->raw === true) {
			$this->showRaw($logReader->display(), $logType, basename($log));
		} else {
			$view = new View('show.phtml');
			$view->output = $logReader->display();
			$view->filters = $this->getFilters();
			$view->log = $log;
			$view->logType = $logType;
			$view->config = $this->config;
			$view->url = 'index.php?' . http_build_query($this->config->getPublic());
			$view->version = $this->config->_version;
			echo $view->render();
		}
	}

	public function resolved() {
		$log = isset($_GET['log']) ? htmlspecialchars($_GET['log']) : null;
		if (preg_match('/exception-[\-0-9]*-[[:alnum:]]*\.html$/i', $log)) {
			if (file_exists($log)) {
				if (file_exists(dirname($log) . '')) unlink(dirname($log) . '/email-sent');
				if (file_exists($log . '.send')) unlink($log . '.send');
				$message = unlink($log) ? 'File ' . $log . ' deleted' : 'File ' . $log . ' not deleted';
			} else {
				$message = 'File ' . $log . ' not found';
			}
		} else {
			$message = 'File ' . $log . ' match exception pattern';
		}

		header('Location: index.php?' . http_build_query($this->config->getPublic()) . '&message=' . urlencode($message));
	}

	private function getFilters() {
		$filters = new Filters($this->config);
		return $filters->render();
	}
}