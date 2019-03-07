<?php namespace MelonCart\Shop\Components;

use October\Rain\Exception\AjaxException;
use URL;
use Cms\Classes\ComponentBase;
use MelonCart\Shop\Classes\Cart as ShoppingCart;
use MelonCart\Shop\Classes\CheckoutData;
use MelonCart\Shop\Classes\ComponentPageHelper;

class Checkout extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Checkout',
            'description' => 'Allows customer to provide their details and complete their order.'
        ];
    }

    public function defineProperties()
    {
        return [
            'isPrimary' => [
                'title'             => 'Primary?',
                'type'              => 'checkbox',
                'description'       => 'Is this the primary page your cart is located on?',
            ],
        ];
    }

    public function onRun()
    {
        $this->prepareVars();
    }

    protected function prepareVars()
    {
        $checkoutData = CheckoutData::instance();
        $componentPageHelper = ComponentPageHelper::instance();

        $this->setPageProp('checkout_step', 'billing_info');
        $this->setPageProp('checkout_data', [
            'billing' => $checkoutData->getBillingInfo(),
            'shipping' => $checkoutData->getShippingInfo(),
        ]);
        $this->setPageProp('cart', ShoppingCart::instance());

        $this->setPageProp('cartPage', $componentPageHelper->cart());
        $this->setPageProp('payPage', $componentPageHelper->pay());
        $this->setPageProp('completePage', $componentPageHelper->complete());
    }

    protected function setPageProp($property, $value)
    {
        $this->page[$property] = $this->{$property} = $value;
    }

    public function onSetBillingInfo()
    {
        $details = post('billing_info', []);
        if ( !is_array($details) || !sizeof($details) )
            throw new AjaxException("Invalid billing information specified.");

        try {
            CheckoutData::instance()->setBillingInfoFromPost($details);
        } catch (ApplicationException $e) {
            throw new AjaxException($e->getMessage());
        }
    }
}
