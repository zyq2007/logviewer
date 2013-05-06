<?php
namespace logviewer;

/**
 * @author Roman Ozana <ozana@omdesign.cz>
 * @author Tomas Ovesny <tomas@wikidi.com>
 */
class Filters {
	/** @var Config */
	private $config;
	/** @var array */
	private $filters = array();

	/**
	 * nacist polozky
	 *
	 * @param Config $config
	 * @internal param \logviewer\polozky $array a hodnoty
	 */
	public function __construct(Config $config) {
		$this->config = $config;
	}

	/**
	 * Vratit filtry jako html
	 *
	 * @return string $html
	 */
	public function render() {
		$view = new View('filters.phtml', false);
		$public = $this->config->getPublic();
		$view->filters = $this->filters;
		$view->items = $this->config->getPublic();
		$view->action = (!empty($_GET['action'])) ? htmlspecialchars($_GET['action']) : null;
		$view->log = (!empty($_GET['log'])) ? htmlspecialchars($_GET['log']) : null;
		$view->listUrl = 'index.php?' . http_build_query($public);


		// raw url downlod
		$public['log'] = $view->log;
		$public['raw'] = true;
		$public['action'] = 'show';
		$view->rawUrl = 'index.php?' . http_build_query($public);
		return $view->render();
	}
}