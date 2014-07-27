<?php

/**
 * Image choser input field with elFinder widget
 *
 * @author Serhiy Shvorob <serges.dev@gmail.com>
 * @author Robert Korulczyk <robert@korulczyk.pl>
 * @link http://rob006.net/
 * @author Bogdan Savluk <Savluk.Bogdan@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
class ServerImageInput extends CInputWidget {

	/**
	 * @var string
	 */
	public $popupConnectorRoute = false;

	/**
	 * @var string
	 */
	public $popupTitle = 'Files';

	/**
	 * HTML options for rendered preview img tag
	 * @var array
	 */
	public $previewImgHtmlOptions = array();

	/**
	 * HTML which will be used as preview if value is empty
	 * @var string
	 */
	public $noImage = '';

	/**
	 * Custom "Browse" button html code
	 * Button id must be according with the pattern [INPUT_FIELD_ID]-browse, for example:
	 * CHtml::button('Browse', array('id' => TbHtml::getIdByName(TbHtml::activeName($model, 'header_box_image')) . '-browse'));
	 * @var string
	 */
	public $customBrowseButton = '';

	/**
	 * Custom "Clear" button html code
	 * Button id must be according with the pattern [INPUT_FIELD_ID]-clear, for example:
	 * CHtml::button('Clear', array('id' => TbHtml::getIdByName(TbHtml::activeName($model, 'header_box_image')) . '-clear'));
	 * @var string
	 */
	public $customClearButton = '';

	/**
	 * Starting `src` value for preview image
	 * @var string
	 */
	public $startPreviewPath = false;

	public function run() {
		ElFinderHelper::registerAssets();

		list($name, $id) = $this->resolveNameID();

		if (isset($this->htmlOptions['id']))
			$id = $this->htmlOptions['id'];
		else
			$this->htmlOptions['id'] = $id;
		if (isset($this->htmlOptions['name']))
			$name = $this->htmlOptions['name'];
		else
			$this->htmlOptions['name'] = $name;

		$contHtmlOptions = $this->htmlOptions;
		$contHtmlOptions['id'] = $id . 'container';
		echo CHtml::openTag('div', $contHtmlOptions);

		if ($this->hasModel()) {
			$value = CHtml::value($this->model, $this->attribute);
			echo CHtml::activeHiddenField($this->model, $this->attribute);
		}
		else {
			$value = $this->value;
			echo CHtml::hiddenField($name, $this->value);
		}

		if ($this->startPreviewPath !== false) {
			$value = $this->startPreviewPath;
		}

		$noImageVisibility = array('style'=>'display: none');
		$previewVisibility = array();

		if (!$value) {
			$noImageVisibility = array();
			$previewVisibility = array('style'=>'display: none');
		}

		echo CHtml::tag('div', array_merge(array('id'=>$id . '-noimage'), $noImageVisibility), $this->noImage);
		echo CHtml::tag(
			'div', 
			array_merge(array('id'=>$id . '-preview'), $previewVisibility), 
			CHtml::image($value, 'preview', $this->previewImgHtmlOptions)
			);

		if (!empty($this->customBrowseButton)) {
			echo $this->customBrowseButton;
		} else {
			echo CHtml::button(
				'Browse',
				array('id' => $id . '-browse', 'class' => 'btn'));
		}

		if (!empty($this->customClearButton)) {
			echo $this->customClearButton;
		} else {
			echo CHtml::button(
				'Clear', 
				array('id' => $id . '-clear', 'class' => 'btn'));
		}

		echo CHtml::closeTag('div');

		// set required options
		if (empty($this->popupConnectorRoute))
			throw new CException('$popupConnectorRoute must be set!');
		$url = Yii::app()->controller->createUrl($this->popupConnectorRoute, array('fieldId' => $id));

		echo <<<FRAME
<div id="{$id}-dialog" style="display:none;" title="{$this->popupTitle}">
<iframe frameborder="0" width="100%" height="100%" src="$url">
</iframe>
</div>
FRAME;

		$js = <<<JS
$("#{$id}-browse, #{$id}-preview img").click(function(){ $(function() {
	$("#{$id}-dialog" ).dialog({
		autoOpen: false,
		position: "center",
		title: "{$this->popupTitle}",
		width: 900,
		height: 750,
		resizable : true,
		modal : true,
	}).dialog( "open" );
});});
 
$("#{$id}-clear").click(function(){ 
	$("#{$id}").val('');
	$("#{$id}-preview").hide();
	$("#{$id}-noimage").show();
});

$("#{$id}").change(function(){
	$("#{$id}-preview img").attr('src', $(this).val());
	$("#{$id}-preview").show();
	$("#{$id}-noimage").hide();
});
JS;

		Yii::app()->getClientScript()->registerScript('ServerImageInput#' . $id, $js);
	}

}
