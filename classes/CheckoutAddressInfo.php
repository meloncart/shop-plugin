<?php namespace MelonCart\Shop\Classes;

use ApplicationException;
use Validator;
use MelonCart\Shop\Models\Customer;
use MelonCart\Shop\Models\ShippingSettings;
use RainLab\Location\Models\State;
use RainLab\Location\Models\Country;

class CheckoutAddressInfo
{
    protected $name = '';
    protected $surname = '';
    protected $email = '';
    protected $company = '';
    protected $phone = '';
    protected $country_id = 0;
    protected $state_id = 0;
    protected $street_addr = '';
    protected $city = '';
    protected $zip = '';
    protected $is_business = false;
    protected $acting_as_billing_info = true;

    /**
     * If true, data will be loaded from/saved to customer billing fields.
     *
     * @param bool $isActing
     * @return $this
     */
    public function actAsBillingInfo($isActing = true)
    {
        $this->acting_as_billing_info = !!$isActing;

        return $this;
    }

    public function copyFromAddressInfo(CheckoutAddressInfo $info)
    {
        $info = $info->get();
        foreach ( $info as $key => $value )
            $this->$key = $value;

        return $this;
    }

    public function loadFromCustomer(Customer $customer)
    {
        if ($this->acting_as_billing_info)
        {
            $this->name = $customer->name;
            $this->surname = $customer->surname;
            $this->email = $customer->email;
            $this->company = $customer->company;
            $this->phone = $customer->phone;
            $this->country_id = $customer->country_id;
            $this->state_id = $customer->state_id;
            $this->street_addr = $customer->street_addr;
            $this->city = $customer->city;
            $this->zip = $customer->zip;
        } else {
            $this->name = $customer->shipping_name;
            $this->surname = $customer->shipping_surname;
            $this->company = $customer->shipping_company;
            $this->phone = $customer->shipping_phone;
            $this->country_id = $customer->shipping_country_id;
            $this->state_id = $customer->shipping_state_id;
            $this->street_addr = $customer->shipping_street_addr;
            $this->city = $customer->shipping_city;
            $this->zip = $customer->shipping_zip;
            $this->is_business = $customer->shipping_addr_is_business;
        }

        return $this;
    }

    public function saveToCustomer(Customer $customer)
    {
        if ($this->acting_as_billing_info)
        {
            $customer->name = $this->name;
            $customer->surname = $this->surname;
            $customer->email = $this->email;
            $customer->company = $this->company;
            $customer->phone = $this->phone;
            $customer->country_id = $this->country_id;
            $customer->state_id = $this->state_id;
            $customer->street_addr = $this->street_addr;
            $customer->city = $this->city;
            $customer->zip = $this->zip;
        } else {
            $customer->shipping_name = $this->name;
            $customer->shipping_surname = $this->surname;
            $customer->shipping_company = $this->company;
            $customer->shipping_phone = $this->phone;
            $customer->shipping_country_id = $this->country_id;
            $customer->shipping_state_id = $this->state_id;
            $customer->shipping_street_addr = $this->street_addr;
            $customer->shipping_city = $this->city;
            $customer->shipping_zip = $this->zip;
            $customer->shipping_addr_is_business = $this->is_business;
        }

        return $this;
    }

    public function validateArray(array $details)
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

        return $validator;
    }

    public function setFromArray(array $details)
    {
        if ( !empty($details['name']) ) $this->name = $details['name'];
        if ( !empty($details['surname']) ) $this->surname = $details['surname'];
        if ( !empty($details['company']) ) $this->company = $details['company'];
        if ( !empty($details['phone']) ) $this->phone = $details['phone'];
        if ( !empty($details['street_addr']) ) $this->street_addr = $details['street_addr'];
        if ( !empty($details['city']) ) $this->city = $details['city'];
        if ( !empty($details['zip']) ) $this->zip = $details['zip'];
        if ( !empty($details['country_id']) ) $this->country_id = intval($details['country_id']);
        if ( !empty($details['state_id']) ) $this->state_id = intval($details['state_id']);
        if ( isset($details['is_business']) ) $this->is_business = !!$details['is_business'];
    }

    public function setFromDefaultShippingLocation()
    {
        $settings = ShippingSettings::instance();

        $this->country_id = $settings->default_country_id;
        $this->state_id = $settings->default_state_id;
        $this->city = $settings->default_city;
        $this->zip = $settings->default_zip;

        return $this;
    }

    public function setLocation($country_id, $state_id, $zip)
    {
        $this->country_id = $country_id;
        $this->state_id = $state_id;
        $this->zip = $zip;
    }

    public function get()
    {
        return [
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'company' => $this->company,
            'phone' => $this->phone,
            'country' => $this->country,
            'state' => $this->state,
            'street_address' => $this->street_address,
            'city' => $this->city,
            'zip' => $this->zip,
            'is_business' => $this->is_business,
            'acting_as_billing_info' => $this->acting_as_billing_info,
        ];
    }

    public function toString()
    {
        if ( !strlen($this->name) )
            return '';

        $country = Country::find( $this->country_id );
        if ( !$country )
            throw new ApplicationException('Country not found');

        $state = null;
        if ( $this->state_id )
        {
            $state = State::where('country_id', '=', $country->id)->find( $this->state_id );
            if ( !$state )
                throw new ApplicationException('State not found');
        }

        $parts = [];
        $parts[] = $this->name.' '.$this->surname;
        if ( strlen($this->company) )
            $parts[] = $this->company;

        $parts[] = $this->zip;
        $parts[] = $this->street_addr;
        $parts[] = $this->city;
        $parts[] = $country->name;
        if ( $state )
            $parts[] = $state->name;

        $result = [];
        $result[] = implode(', ', $parts);

        if ( $this->email )
            $result[] = $this->email;

        if ( $this->phone )
            $result[] = 'Phone: '.$this->phone;

        return implode('. ', $result);
    }
}