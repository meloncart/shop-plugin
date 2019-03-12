<?php namespace MelonCart\Shop\Controllers;

use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use Illuminate\Support\Arr;
use MelonCart\Shop\Models\Order;
use MelonCart\Shop\Models\OrderStatus;
use MelonCart\Shop\Models\OrderStatusTransition;

/**
 * Orders Back-end Controller
 */
class Orders extends Controller
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

        BackendMenu::setContext('MelonCart.Shop', 'shop', 'orders');
    }

    public function index()
    {
        $this->addJs('/plugins/meloncart/shop/assets/js/bulk-actions.js');

        $this->asExtension('ListController')->index();
    }

    public function index_onUpdateStatus()
    {
        $checkedIds = post('checked');
        $newStatus = post('status');

        if ( !is_array($checkedIds) || !count($checkedIds) )
        {
            Flash::error("You must select at least one order.");
            return $this->listRefresh();
        }

        $orders = Order::whereIn('id', $checkedIds)->get();
        $status_ids = $orders->pluck('status_id')->unique()->toArray();

        if ( count($status_ids) > 1 )
        {
            Flash::error("You may only perform this bulk action on orders of the same status.");
            return $this->listRefresh();
        }

        foreach ( $orders as $order )
            $order->updateStatus($newStatus);

        Flash::success("Order status successfully changed.");
        return $this->listRefresh();
    }

    /**
     * Perform bulk action on selected users
     */
    public function index_onBulkAction()
    {
        if (
            ($bulkAction = post('action')) &&
            ($checkedIds = post('checked')) &&
            is_array($checkedIds) &&
            count($checkedIds)
        ) {
            if ( in_array($bulkAction, ['onLoadStatusSelection']) )
            {
                $status_ids = Order::select('status_id')
                    ->whereIn('id', $checkedIds)
                    ->distinct()
                    ->pluck('status_id')
                    ->toArray();

                if ( count($status_ids) > 1 )
                {
                    Flash::error("You may only perform this bulk action on orders of the same status.");
                    return $this->listRefresh();
                }

                if ( $bulkAction == 'onLoadStatusSelection' )
                {
                    $this->vars = [
                        'action' => $bulkAction,
                        'checked' => $checkedIds,
                        'statuses' => OrderStatusTransition::with('to_status')
                            ->where('from_status_id', $status_ids[0])
                            ->get()
                            ->pluck('to_status')
                    ];
                    return $this->makePartial('new_status_selection');
                };
            }

            foreach ($checkedIds as $orderId) {
                if (!$order = Order::find($orderId)) {
                    continue;
                }

                switch ($bulkAction) {
                    case 'delete':
                        $order->forceDelete();
                        break;
                }
            }

            Flash::success(Lang::get('rainlab.user::lang.users.'.$bulkAction.'_selected_success'));
        }
        else {
            Flash::error(Lang::get('rainlab.user::lang.users.'.$bulkAction.'_selected_empty'));
        }

        return $this->listRefresh();
    }
}
