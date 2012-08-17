<?php
namespace logviewer;
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 * @author Tomas Ovesny <tomas@wikidi.com>
 */
class View {
	/** @var string */
	private $templateDir = '';
	/** @var array */
	private $data = array();
	/** @var string */
	private $template = '';
	/** @var bool */
	private $layout = true;

	/**
	 * @param string $template
	 * @param bool $layout
	 * @throws \Exception
	 */
	public function __construct($template, $layout = true) {
		$this->templateDir = realpath(__DIR__) . '/../templates';
		$this->template = $this->templateDir . '/' . $template;
		if (!file_exists($this->template)) throw new \Exception(sprintf('Template "%s" not exists', $this->template));
		$this->layout = $layout;
	}

	public function render() {
		// view
		ob_start();
		extract($this->data);
		include $this->template;
		$content = ob_get_contents();
		ob_clean();

		// use layout
		if ($this->layout) {
			ob_start();
			include $this->templateDir . '/@layout.phtml'; // $content
			$html = ob_get_contents();
			ob_clean();
		} else {
			$html = $content;
		}

		return $html;
	}

	public function __set($name, $value) {
		$this->data[$name] = $value;
	}

	public function __get($name) {
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
	}
}