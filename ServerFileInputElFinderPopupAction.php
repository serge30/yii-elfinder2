<?php

/**
 * Action creates content for file browse popup window with elFinder widget
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 * @link http://rob006.net/
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
class ServerFileInputElFinderPopupAction extends CAction {

	/**
	 * @var string
	 */
	public $connectorRoute = false;

	/**
	 * Popup title
	 * @var string
	 */
	public $title = 'Files';

	/**
	 * Client settings.
	 * @see https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
	 * @var array
	 */
	public $settings = array();

	public function run() {
		ElFinderHelper::registerAssets();

		// set required options
		if (empty($this->connectorRoute))
			throw new CException('$connectorRoute must be set!');
		$settings = array(
			'url' => $this->controller->createUrl($this->connectorRoute),
			'lang' => Yii::app()->language,
		);

		echo $this->controller->processOutput(
			$this->controller->renderFile(
				__DIR__.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'ServerFileInputElFinderPopupAction.php', 
				array(
					'title' => $this->title, 
					'settings' => CJavaScript::encode($settings), 
					'fieldId' => $_GET["fieldId"]),
				true
				)
			);
	}

}
