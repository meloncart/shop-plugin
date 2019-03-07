<?php namespace MelonCart\Shop\Models;

use Model;
use RainLab\Location\Models\State;
use RainLab\Location\Models\Country;

/**
 * Customer Model
 */
class Customer extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'meloncart_shop_customers';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'orders' => ['RainLab\User\Models\Order'],
    ];
    public $belongsTo = [
        'user' => ['RainLab\User\Models\User'],
        'country' => ['RainLab\Location\Models\Country'],
        'state' => ['RainLab\Location\Models\State'],
        'shipping_country' => ['RainLab\Location\Models\Country'],
        'shipping_state' => ['RainLab\Location\Models\State'],
    ];
    public $belongsToMany = [
        'orders' => ['MelonCart\Shop\Models\Order', 'table' => 'meloncart_shop_products_orders', 'timestamps' => true],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public function getCountryOptions()
    {
        return Country::getNameList();
    }

    public function getStateOptions()
    {
        if ( $this->user )
            return State::getNameList(post('Customer.country', $this->user->country_id ?: 1));
        else
            return State::getNameList(post('Customer.country', 1));
    }

    public function getShippingCountryOptions()
    {
        return Country::getNameList();
    }

    public function getShippingStateOptions()
    {
        return State::getNameList(post('Customer.shipping_country', $this->shipping_country_id ?: 1));
    }

    public function beforeSave()
    {
        if ( $this->user && $this->user->isDirty() )
            $this->user->save();
    }

    public function getCountryIdAttribute() { return $this->user ? $this->user->country_id : null; }
    public function setCountryIdAttribute($value) { if ($this->user) $this->user->country_id = $value; }

    public function getCountryAttribute() { return $this->user ? $this->user->country : null; }
    public function setCountryAttribute($value) { if ($this->user) $this->user->country = $value; }

    public function getStateIdAttribute() { return $this->user ? $this->user->state_id : null; }
    public function setStateIdAttribute($value) { if ($this->user) $this->user->state_id = $value; }

    public function getStateAttribute() { return $this->user ? $this->user->state : null; }
    public function setStateAttribute($value) { if ($this->user) $this->user->state = $value; }

    public function getNameAttribute() { return $this->user ? $this->user->name : null; }
    public function setNameAttribute($value) { if ($this->user) $this->user->name = $value; }

    public function getSurnameAttribute() { return $this->user ? $this->user->surname : null; }
    public function setSurnameAttribute($value) { if ($this->user) $this->user->surname = $value; }

    public function getEmailAttribute() { return $this->user ? $this->user->email : null; }
    public function setEmailAttribute($value) { if ($this->user) $this->user->email = $value; }

    public function getCompanyAttribute() { return $this->user ? $this->user->company : null; }
    public function setCompanyAttribute($value) { if ($this->user) $this->user->company = $value; }

    public function getPhoneAttribute() { return $this->user ? $this->user->phone : null; }
    public function setPhoneAttribute($value) { if ($this->user) $this->user->phone = $value; }

    public function getStreetAddrAttribute() { return $this->user ? $this->user->street_addr : null; }
    public function setStreetAddrAttribute($value) { if ($this->user) $this->user->street_addr = $value; }

    public function getCityAttribute() { return $this->user ? $this->user->city : null; }
    public function setCityAttribute($value) { if ($this->user) $this->user->city = $value; }

    public function getZipAttribute() { return $this->user ? $this->user->zip : null; }
    public function setZipAttribute($value) { if ($this->user) $this->user->zip = $value; }
}
