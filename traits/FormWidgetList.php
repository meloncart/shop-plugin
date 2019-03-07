<?php namespace MelonCart\Shop\Traits;

use Lang;
use System\Classes\SystemException;

trait FormWidgetList
{
	/**
     * @var array List definitions, keys for alias and value for configuration.
     */
    protected $fwl_listDefinitions;

    /**
     * @var string The primary list alias to use. Default: list
     */
    protected $fwl_primaryDefinition;

    /**
     * @var Backend\Classes\WidgetBase Reference to the list widget object.
     */
    protected $fwl_listWidgets = [];

    /**
     * {@inheritDoc}
     */
    protected $fwl_requiredProperties = ['listConfig'];

    /**
     * @var WidgetBase Reference to the toolbar widget objects.
     */
    protected $fwl_toolbarWidgets = [];

    /**
     * @var array Configuration values that must exist when applying the primary config file.
     * - modelClass: Class name for the model
     * - list: List column definitions
     */
    protected $requiredConfig = ['modelClass', 'list'];

	public function fwl_generate($config_list)
	{
		$this->fwl_listDefinitions = ['list' => $config_list];
        $this->fwl_primaryDefinition = 'list';

        $config = $this->makeConfig($this->fwl_listDefinitions[$this->fwl_primaryDefinition]);

        $this->fwl_makeLists();

        return $this->fwl_listRender();
	}

	/**
     * Creates all the list widgets based on the definitions.
     * @return array
     */
    public function fwl_makeLists()
    {
        foreach ($this->fwl_listDefinitions as $definition => $config) {
            $this->fwl_listWidgets[$definition] = $this->fwl_makeList($definition);
        }

        return $this->fwl_listWidgets;
    }

    /**
     * Prepare the widgets used by this action
     * @return void
     */
    public function fwl_makeList($definition = null)
    {
        if (!$definition || !isset($this->fwl_listDefinitions[$definition]))
            $definition = $this->fwl_primaryDefinition;

        $listConfig = $this->makeConfig($this->fwl_listDefinitions[$definition]);

        /*
         * Create the model
         */
        $class = $listConfig->modelClass;
        $model = new $class();
        $model = $this->controller->listExtendModel($model, $definition);

        /*
         * Prepare the list widget
         */
        $columnConfig = $this->makeConfig($listConfig->list);
        $columnConfig->model = $model;
        $columnConfig->alias = $definition;
        if (isset($listConfig->recordUrl)) $columnConfig->recordUrl = $listConfig->recordUrl;
        if (isset($listConfig->recordOnClick)) $columnConfig->recordOnClick = $listConfig->recordOnClick;
        if (isset($listConfig->recordsPerPage)) $columnConfig->recordsPerPage = $listConfig->recordsPerPage;
        if (isset($listConfig->noRecordsMessage)) $columnConfig->noRecordsMessage = $listConfig->noRecordsMessage;
        if (isset($listConfig->defaultSort)) $columnConfig->defaultSort = $listConfig->defaultSort;
        if (isset($listConfig->showSorting)) $columnConfig->showSorting = $listConfig->showSorting;
        if (isset($listConfig->showSetup)) $columnConfig->showSetup = $listConfig->showSetup;
        if (isset($listConfig->showCheckboxes)) $columnConfig->showCheckboxes = $listConfig->showCheckboxes;
        if (isset($listConfig->showTree)) $columnConfig->showTree = $listConfig->showTree;
        if (isset($listConfig->treeExpanded)) $columnConfig->treeExpanded = $listConfig->treeExpanded;
        $widget = $this->makeWidget('Backend\Widgets\Lists', $columnConfig);
        $widget->bindToController();

        /*
         * Extensibility helpers
         */
        $widget->bindEvent('list.extendQueryBefore', function($query) use ($definition) {
            $this->controller->listExtendQueryBefore($query, $definition);
        });

        $widget->bindEvent('list.extendQuery', function($query) use ($definition) {
            $this->controller->listExtendQuery($query, $definition);
        });

        $widget->bindEvent('list.injectRowClass', function($record) use ($definition) {
            return $this->controller->listInjectRowClass($record, $definition);
        });

        $widget->bindEvent('list.overrideColumnValue', function($record, $column, $value) use ($definition) {
            return $this->controller->listOverrideColumnValue($record, $column->columnName, $definition);
        });

        $widget->bindEvent('list.overrideHeaderValue', function($column, $value) use ($definition) {
            return $this->controller->listOverrideHeaderValue($column->columnName, $definition);
        });

        /*
         * Prepare the toolbar widget (optional)
         */
        if (isset($listConfig->toolbar)) {
            $toolbarConfig = $this->makeConfig($listConfig->toolbar);
            $toolbarConfig->alias = $widget->alias . 'Toolbar';
            $toolbarWidget = $this->makeWidget('Backend\Widgets\Toolbar', $toolbarConfig);
            $toolbarWidget->cssClasses[] = 'list-header';

            /*
             * Link the Search Widget to the List Widget
             */
            if ($searchWidget = $toolbarWidget->getSearchWidget()) {
                $searchWidget->bindEvent('search.submit', function() use ($widget, $searchWidget) {
                    $widget->setSearchTerm($searchWidget->getActiveTerm());
                    return $widget->onRefresh();
                });

                // Find predefined search term
                $widget->setSearchTerm($searchWidget->getActiveTerm());
            }

            $this->fwl_toolbarWidgets[$definition] = $toolbarWidget;
        }

        return $widget;
    }

    /**
     * Renders the widget collection.
     * @param  string $definition Optional list definition.
     * @return string Rendered HTML for the list.
     */
    public function fwl_listRender($definition = null)
    {
        if (!count($this->fwl_listWidgets))
            throw new SystemException(Lang::get('backend::lang.list.behavior_not_ready'));

        if (!$definition || !isset($this->fwl_listDefinitions[$definition]))
            $definition = $this->fwl_primaryDefinition;

        $collection = [];

        if (isset($this->fwl_toolbarWidgets[$definition]))
            $collection[] = $this->fwl_toolbarWidgets[$definition]->render();

        $collection[] = $this->fwl_listWidgets[$definition]->render();

        return implode(PHP_EOL, $collection);
    }
}
