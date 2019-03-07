<?php namespace MelonCart\Shop\ShippingTypes;

use DB;
use Backend\Widgets\Form;
use Backend\Widgets\Table;
use Backend\Classes\Controller;
use RainLab\Location\Models\Country;
use RainLab\Location\Models\State;
use MelonCart\Shop\ShippingTypes\ShippingTypeBase;

class TableRate extends ShippingTypeBase
{
    public function extend_config_form(Form $form)
    {
        $form->addFields([
            'config_data[rates]' => [
                'label'   => 'Rates',
                'type'    => 'datatable',
                'tab'     => 'Rates',
                'columns' => [
                    'country' => [
                        'title' => 'Country Code',
                        'type' => 'dropdown',
                        'strict' => false,
                    ],

                    'state' => [
                        'title' => 'State Code',
                        'type' => 'dropdown',
                        'strict' => false,
                        'dependsOn' => 'country',
                    ],

                    'zip' => [
                        'title' => 'Zip',
                    ],

                    'city' => [
                        'title' => 'City',
                    ],

                    'min_weight' => [
                        'title' => 'Min Weight',
                        'type' => 'string',
                    ],

                    'max_weight' => [
                        'title' => 'Max Weight',
                        'type' => 'string',
                    ],

                    'min_volume' => [
                        'title' => 'Min Volume',
                        'type' => 'string',
                    ],

                    'max_volume' => [
                        'title' => 'Max Volume',
                        'type' => 'string',
                    ],

                    'min_subtotal' => [
                        'title' => 'Min Subtotal',
                        'type' => 'string',
                    ],

                    'max_subtotal' => [
                        'title' => 'Max Subtotal',
                        'type' => 'string',
                    ],

                    'min_items' => [
                        'title' => 'Min Items',
                        'type' => 'string',
                    ],

                    'max_items' => [
                        'title' => 'Max Items',
                        'type' => 'string',
                    ],

                    'price' => [
                        'title' => 'Rate',
                        'type' => 'string',
                    ],
                ],
            ],
        ], 'primary');
    }

    public function create(Controller $controller)
    {
        $widget = $controller->formGetWidget()->getFormWidget('config_data[rates]');
        if ( !$widget )
            return;

        $this->bindTableEvents($widget->getTable());
    }

    public function update(Controller $controller, $recordId)
    {
        $widget = $controller->formGetWidget()->getFormWidget('config_data[rates]');
        if ( !$widget )
            return;

        $this->bindTableEvents($widget->getTable());
    }

    public function bindTableEvents(Table $table)
    {
        $table
            ->bindEvent('table.getAutocompleteOptions', array($this, 'getAutocompleteOptions'))
            ->unbindEvent('table.getDropdownOptions')
            ->bindEvent('table.getDropdownOptions', array($this, 'getDdropdownOptions'));
    }

    public function getAutocompleteOptions($columnName, $rowData)
    {
        switch ( $columnName )
        {
            default:
                return [];
        }
    }

    public function getDdropdownOptions($field, $data)
    {
        switch ( $field )
        {
            case 'country': return $this->getCountryList(array_get($data, $field));
            case 'state': return $this->getStateList(array_get($data, 'country'), array_get($data, $field));
            default: return [];
        }
    }

    protected function getCountryList($term)
    {
        $result = Country::select(DB::raw("CONCAT_WS(' - ', code, name) AS full_name, code"))
            ->orderBy('name')
            ->lists('full_name', 'code');

        $result = ['*' => '* - Any country'] + $result;

        return $result;
    }

    protected function getStateList($countryCode, $term)
    {
        $result = ['*' => '* - Any state'];

        if (!$countryCode || $countryCode == '*')
            return $result;

        $states = State::select(DB::raw("CONCAT_WS(' - ', code, name) AS full_name, code"))
            ->whereHas('country', function($query) use ($countryCode) {
                $query->where('code', $countryCode);
            })
            ->orderBy('name')
            ->lists('full_name', 'code');

        $result = $result + $states;

        return $result;
    }
}
