<?php namespace MelonCart\Shop\FormWidgets;

use DB;
use Exception;
use Request;
use Backend\Classes\FormWidgetBase;
use MelonCart\Shop\Models\Product;

/**
 * Product Options List
 * Renders the options matrix.
 */
class ListRelation extends FormWidgetBase
{
	// use \MelonCart\Shop\Traits\FormWidgetList;

	/**
	 * {@inheritDoc}
	 */
	public $defaultAlias = 'listrelation';

	/**
	 * @var \Backend\Classes\WidgetBase Reference to the widget used for viewing (list or form).
	 */
	protected $listWidget;

	protected $toolbarWidget;

	public function init()
	{

	}

	/**
	 * Prepares the list data
	 */
	public function prepareVars()
	{

	}

	public function render()
	{
		$this->prepareVars();
		return $this->makePartial('widget');
	}
}
