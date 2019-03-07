<?php namespace MelonCart\Shop\Classes;

use Session;
use System\Classes\ApplicationException;
use Validator;
use MelonCart\Shop\Models\Customer;
use MelonCart\Shop\Models\ShippingSettings;
use MelonCart\Shop\Classes\CheckoutAddressInfo;
use RainLab\Location\Models\State;
use RainLab\Location\Models\Country;

class CheckoutData
{
    use \October\Rain\Support\Traits\Singleton;

    const SESSION_NAME = 'meloncart_checkoutdata';

    /**
     * @var array   Array of checkout info. See defaultData() method
     */
    protected $data = [];

    /**
     * Initialize the singleton free from constructor parameters.
     */
    protected function init()
    {
        $this->load();
    }

    protected function load()
    {
        $data = Session::get(self::SESSION_NAME, '[]');
        if ( is_string($data) && $data )
        {
            $data = json_decode($data, true);
            if ( $data )
                $this->data = $data;
        }

        // No data loaded? set default data
        if ( !$this->data )
            $this->data = $this->defaultData();
    }

    public function copyBillingToShipping()
    {
        $this->data['shipping_info'] = $this->data['billing_info'];
    }

    public function getBillingInfo()
    {
        return $this->data['billing_info'];
    }

    public function getPaymentMethod()
    {
        return $this->data['payment_method'];
    }

    public function getShippingInfo()
    {
        return $this->data['shipping_info'];
    }

    public function loadFromCustomer(Customer $customer, $force = false)
    {
        if (!empty($this->data['billing_info']) && !$force)
            return;

        // Load billing info
        $this->data['billing_info'] = (new CheckoutAddressInfo())
            ->loadFromCustomer($customer)
            ->get();

        // Load shipping info
        $this->data['shipping_info'] = (new CheckoutAddressInfo())
            ->actAsBillingInfo(true)
            ->loadFromCustomer($customer)
            ->get();
    }

    public function setBillingInfoFromPost(array $new_details)
    {
        $old_details = $this->data['billing'];
        $new_details = array_only($new_details, array_keys($old_details));

        $details = array_merge($old_details, $new_details);
        $this->validateAddressDetails($details);

        $this->data['billing'] = $details;
        $this->save();
    }

    public function validateAddressDetails(array $details)
    {
        $validator = Validator::make($details, [
            'name' => ['required'],
            'surname' => ['required'],
            'email' => ['required', 'email'],
            'street_addr' => ['required'],
            'city' => ['required'],
            'zip' => ['required'],
            'country_id' => ['required', 'integer'],
            'state_id' => ['integer'],
        ]);
        // State field required if country has states
        $validator->sometimes('valid_state', 'required', function($details) {
            return State::whereCountryId($details['country_id'])->exists();
        });

        if ( $validator->fails() )
            throw new ApplicationException($validator->messages()->first());
    }

    public function setBillingInfo(Customer $customer, array $info = [])
    {
        if ( empty($info) )
        {
            $info = $this->getBillingInfo();
            //$info->set_from_post($customer);
        } else
            ;//$info->actAsBillingInfo(true);

        $this->data['billing_info'] = $info;
    }

    protected function defaultData()
    {
        $settings = ShippingSettings::instance();

        return [
            'billing_info' => [
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'phone' => '',
                'company' => '',
                'street_addr' => '',
                'country_id' => $settings->default_country_id,
                'state_id' => $settings->default_state_id,
                'city' => $settings->default_city,
                'zip' => $settings->default_zip,
            ],
            'shipping_info' => [
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'phone' => '',
                'company' => '',
                'street_addr' => '',
                'country_id' => $settings->default_country_id,
                'state_id' => $settings->default_state_id,
                'city' => $settings->default_city,
                'zip' => $settings->default_zip,
            ],
            'register_customer' => false,
            'customer_password' => '',
            'shipping_method' => [],
            'payment_method' => [
                'id' => null,
                'name' => null,
                'melon_api_code' => null
            ],
            'coupon_code' => [],
            'cart_id' => 0,
            'customer_notes' => '',
        ];
    }

    public static function reset()
    {
        Session::put(self::SESSION_NAME, json_encode([]));
    }

    protected function save()
    {
        $data = json_encode($this->data);
        Session::put(self::SESSION_NAME, $data);
    }
}
