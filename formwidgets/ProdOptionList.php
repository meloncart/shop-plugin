<?php namespace MelonCart\Shop\FormWidgets;

use DB;
use Request;
use Backend\Classes\FormWidgetBase;
use MelonCart\Shop\Models\Product;
use MelonCart\Shop\Models\ProductOMOption;
use MelonCart\Shop\Models\ProductOMRecord;
use Illuminate\Database\Eloquent\Collection;
use Exception;

/**
 * Product Options List
 * Renders the options matrix.
 */
class ProdOptionList extends FormWidgetBase
{
	// use \MelonCart\Shop\Traits\FormWidgetList;

	/**
	 * {@inheritDoc}
	 */
	public $defaultAlias = 'prodoptionlist';

	/**
	 * @var Backend\Classes\WidgetBase Reference to the widget used for viewing (list or form).
	 */
	protected $listWidget;

	protected $toolbarWidget;

	public function init()
	{
		$this->listWidget = $this->makeOMRecordsListWidget();
		$this->listWidget->bindToController();

		$this->toolbarWidget = $this->makeOMRecordsToolbarWidget();
		$this->toolbarWidget->bindToController();

		$this->controller->addJs('/plugins/meloncart/shop/formwidgets/prodoptionlist/assets/js/om.js');
	}

	/**
	 * Prepares the list data
	 */
	public function prepareVars()
	{
		$sessionKey = $this->controller->widget->formOptionsList->sessionKey;
		$columnName = $this->columnName;

		$this->vars['name'] = $this->formField->getName();
		// $this->vars['value'] = $this->model->$columnName()->withDeferred($sessionKey)->get();
		$this->vars['listWidget'] = $this->listWidget;
		$this->vars['toolbarWidget'] = $this->toolbarWidget;
	}

	protected function makeOMRecordsListWidget()
	{
		// dd($this->config);
		$config = $this->config->listOptions;
		$model = new $config['modelClass'];
		$config['alias'] = $this->alias . 'List';
		$config['model'] = $model;
		// JS doesn't have access to formwidget record handlers - so replace :handler in our onClick func to the actual handler
		$config['recordOnClick'] = str_replace(':handler', "'".$this->getEventHandler('onLoadRecordModal')."'", $config['recordOnClick']);

		// Grab a list of columns to display
		$columns = $this->makeConfig($config['list'])->columns;

		// Add option columns
		$customColumns = $this->model->optionNames;
		foreach ( $customColumns as $customColumn )
		{
			$columns = [
				$customColumn->code => [
					'label' => $customColumn->title,
					'sortable' => true,
					'searchable' => true,
					'select' => 'omro'.$customColumn->id.'.value', //This must be the same as ProductOMRecord::scopeIncludeOptions() ro_alias
				]
			] + $columns;
		}
		$config['columns'] = $columns;


		$config = $this->makeConfig($config);
		$widget = $this->makeWidget('Backend\Widgets\Lists', $config);

		$widget->bindEvent('list.extendQuery', function($query) use ($model, $customColumns) {
			// Include option column data
			$query = $model->scopeIncludeOptions($query, $customColumns->lists('code', 'id'));

			// Only show records for this product
			if ( $this->model->id )
				$query->where($model->getTable().'.product_id', '=', $this->model->id);
					// ->groupBy($model->getQualifiedKeyName()); // Just in case
			else
				; // @TODO Include IDs of deferred rows $query->where('product_id', 'in', $deferred_records);

			return $query;
		});

		return $widget;
	}

	public function makeOMRecordsToolbarWidget()
	{
		$listWidget = $this->listWidget;

		$toolbarConfig = $this->makeConfig($this->config->listOptions['toolbar']);
        $toolbarConfig->alias = $listWidget->alias . 'Toolbar';
        $toolbarWidget = $this->makeWidget('Backend\Widgets\Toolbar', $toolbarConfig);
        $toolbarWidget->cssClasses[] = 'list-header';

        /*
         * Link the Search Widget to the List Widget
         */
        if ($searchWidget = $toolbarWidget->getSearchWidget()) {
            $searchWidget->bindEvent('search.submit', function() use ($listWidget, $searchWidget) {
                $listWidget->setSearchTerm($searchWidget->getActiveTerm());
                return $listWidget->onRefresh();
            });

            // Find predefined search term
            $listWidget->setSearchTerm($searchWidget->getActiveTerm());
        }

        return $toolbarWidget;
	}

	public function render()
	{
		$this->prepareVars();
		return $this->makePartial('recordlist');
	}

	/*
	 * Misc
	 */

	/**
	 * Display a new copy of the OM list
	 */
	public function onRefreshRecords()
	{
		$this->prepareVars();
		return [
			'#prodOptionsFormWidget' => $this->makePartial('recordlist'),
		];
	}

	public function onGenerateRecords()
	{
		// All these DB queries take up a huge amount of memory when logging. So turn it off.
		DB::disableQueryLog();

		// Grab a list of option IDs and their possible values (om_option_id, value fields)
		$sessionKey = $this->controller->widget->formOptionsList->sessionKey;
		$options = $this->model->optionNames()->select('id', 'values')->withDeferred($sessionKey)->get()->each(function($item) {
			$item->values = explode("\n", $item->values);
			return $item;
		})->lists('values', 'id');

		// Generate all permutations of [om_id, value] grouped by om_id
		$results = [];
		foreach ( $options as $option_id => $values )
		{
			$results[$option_id] = [];
			foreach ( $values as $value )
				$results[$option_id][] = [$option_id, $value];
		}

		// Generate option permutations a record may have grouped by permutation
		$permutations = ProductOMOption::generate_permutations($results);
		$record_table = (new ProductOMRecord)->getTable();

		foreach ( $permutations as $permutation )
		{
			// Duplicate not found? Create a record and set its options
			if ( !ProductOMRecord::withOptionValues($permutation)->exists() )
			{
				// Create the record
				$record_id = DB::table($record_table)->insertGetId([
					'product_id' => $this->model->id
				]);
				// Set the records options
				foreach ( $permutation as $key => $tuple )
				{
					$tuple[] = $record_id;
					// We need to use INSERT IGNORE here, so do a DB::insert() manually
					DB::insert('INSERT IGNORE INTO meloncart_shop_product_om_record_options (om_option_id, value, om_record_id) VALUES (?,?,?)', $tuple);
				}
			}
		}

		return $this->onRefreshRecords();
	}




	/*
	 * Options modal
	 */

	public function onLoadOptionsModal()
	{
		/*
		 * Create a form widget to render the Add form
		 */
		$config = $this->makeConfig('$/meloncart/shop/models/productomoption/fields.yaml');
		$config->model = new ProductOMOption;
		$config->context = 'create';
		$form = $this->makeWidget('Backend\Widgets\Toolbar', $config);

		// $this->vars['list'] = $this->fwl_generate('$/meloncart/shop/controllers/productomoptions/config_list.yaml');

		$sessionKey = $this->controller->widget->formOptionsList->sessionKey;
		$this->vars['form'] = $form;
		$this->vars['options'] = $this->model->optionNames()->withDeferred($sessionKey)->get();

		return $this->makePartial('modal_optionslist');
	}

	public function onLoadCreateOptionModal()
	{
		/*
		 * Create a form widget to render the Add form
		 */
		$config = $this->makeConfig('$/meloncart/shop/models/productomoption/fields.yaml');
		$config->model = new ProductOMOption;
		$config->context = 'create';
		$form = $this->makeWidget('Backend\Widgets\Form', $config);

		$this->vars['form'] = $form;

		return $this->makePartial('modal_option_add');
	}

	public function onLoadUpdateOptionModal()
	{
		try {
			$id = post('id', 0);
			if ( !$option = ProductOMOption::find($id) )
				throw new Exception('Product Option not found.');

			$this->vars['id'] = $id;

			/*
			 * Create a form widget to render the form
			 */
			$config = $this->makeConfig('$/meloncart/shop/models/productomoption/fields.yaml');
			$config->model = $option;
			$config->context = 'edit';
			$form = $this->makeWidget('Backend\Widgets\Form', $config);

			$this->vars['form'] = $form;
		}
		catch (Exception $ex) {
			$this->vars['fatalError'] = $ex->getMessage();
		}

		return $this->makePartial('modal_option_update');
	}

	public function onCreateOption()
	{
		$option = new ProductOMOption;
		$option->fill(Request::input());
		$option->save();

		$this->model->optionNames()->add($option, Request::input('_session_key'));

		return $this->onLoadOptionsModal();
	}

	public function onUpdateOption()
	{
		$id = post('id', 0);
		if (!$option = ProductOMOption::find($id))
			throw new Exception('Product Option not found.');

		$option->fill(Request::input());
		$option->save();

		$this->prepareVars();
		return $this->onLoadOptionsModal();
	}

	public function onDeleteOptions()
	{
		$id = post('id', 0);
		if (!$options = ProductOMOption::find($id))
			throw new Exception('Product Option not found.');

		foreach ( $options as $option )
			$option->delete();

		$this->prepareVars();
		return $this->onLoadOptionsModal();
	}



	/*
	 * Records
	 */
	public function onLoadRecordModal()
	{
		$id = Request::input('id');
		if ($id && !$record = ProductOMRecord::find($id))
			throw new Exception('Option Matrix record not found.');
		else
			$record = new ProductOMRecord;

		/*
		 * Create a form widget to render the Add form
		 */
		$config = $this->makeConfig('$/meloncart/shop/models/productomrecord/fields.yaml');
		$config->model = $record;
		$config->context = $id ? 'update' : 'create';
		$form = $this->makeWidget('Backend\Widgets\Form', $config);
		$sessionKey = $this->controller->widget->formOptionsList->sessionKey;

		$options = $this->model->optionNames()->withDeferred($sessionKey)->get();
		foreach ( $options as $option )
			$form->addFields([
				$option->code => [
					'label' => $option->title,
					'type' => 'dropdown',
					'options' => explode("\n", $option->values),
					'tab' => 'Options',
				],
			], 'primary');

		$this->vars['form'] = $form;
		$this->vars['id'] = $id;
		$this->vars['options'] = $this->model->optionNames()->withDeferred($sessionKey)->get();

		return $this->makePartial('modal_record');
	}

	public function onSaveRecord()
	{
		$id = post('id', 0);
		if ( !$id )
			$record = new ProductOMRecord;
		elseif ( !$record = ProductOMRecord::find($id) )
			throw new Exception('Option Matrix record not found.');

		$data = Request::input();
		if ( $data['hide_if_out_of_stock'] == -1 )
			$data['hide_if_out_of_stock'] = null;
		if ( $data['track_inventory'] == -1 )
			$data['track_inventory'] = null;

		$record->fill($data);
		$record->save();

		return $this->onRefreshRecords();
	}
}
