<?php namespace MelonCart\Shop\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Backend\Widgets\Form;
use MelonCart\Shop\Classes\ShippingTypeManager;

/**
 * ShippingMethods Back-end Controller
 */
class ShippingMethods extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('MelonCart.Shop', 'meloncart', 'shippingmethods');
    }

    /**
     * Extend supplied model used by create and update actions, the model can
     * be altered by overriding it in the controller.
     * @param Model $model
     * @return Model
     */
    public function formExtendModel($model)
    {
        if ( !$model->id )
            $model->type = $this->formGetContext();
        return $model;
    }

    /**
     * Called after the form fields are defined.
     * @param Backend\Widgets\Form $host The hosting form widget
     * @param array $fields
     * @return void
     */
    public function formExtendFields(Form $form, array $fields)
    {
        $class = $form->model->type;
        $type = ShippingTypeManager::instance()->resolveItemType( $class );

        if ( $type )
        {
            (new $class)->extend_config_form($form);
            $this->vars['shippingType'] = $type;
        }
    }

    public function onLoadTypeSelection()
    {
        $this->vars['item_types'] = ShippingTypeManager::instance()->listItemTypes();
        return $this->makePartial('type_selection');
    }

    /**
     * Create Controller action
     * @param string $context Explicitly define a form context.
     * @return void
     */
    public function create($context = null)
    {
        $this->asExtension('FormController')->create($context);

        $type = ShippingTypeManager::instance()->resolveItemType( $context );
        if ( $type )
        {
            $this->vars['formModel']->type = $context;
            (new $context)->create($this);
        }
    }

    /**
     * Called before the creation form is saved.
     * @param Model
     */
    public function formBeforeCreate($model)
    {
        $model->type = $this->formGetContext();
    }

    /**
     * Edit Controller action
     * @param int $recordId The model primary key to update.
     * @param string $context Explicitly define a form context.
     * @return void
     */
    public function update($recordId = null, $context = null)
    {
        $this->asExtension('FormController')->update($recordId, $context);

        if ( $this->vars['formModel'] )
        {
            $methodClass = $this->vars['formModel']->type;
            $type = ShippingTypeManager::instance()->resolveItemType( $methodClass );
            if ( $type )
                (new $methodClass)->update($this, $recordId);
        }
    }
}
