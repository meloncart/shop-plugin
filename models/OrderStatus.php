<?php namespace MelonCart\Shop\Models;

use Illuminate\Support\Arr;
use Model;

/**
 * OrderStatus Model
 */
class OrderStatus extends Model
{

    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'meloncart_shop_order_statuses';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'transitions' => ['MelonCart\Shop\Models\OrderStatusTransition', 'key' => 'from_status_id'],
    ];
    public $belongsTo = [];
    public $belongsToMany = [
        //'transitions' => [self::class, 'table' => 'meloncart_shop_order_status_transitions', 'key' => 'from_status_id', 'otherKey' => 'to_status_id'],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * @var array Validation rules
     */
    public $rules = [];

    public function beforeDelete()
    {
        $this->transitions()->delete();
    }

    public function getAvailableMessageTemplates()
    {
        $templates = \System\Models\MailTemplate::allTemplates();

        return Arr::pluck($templates, 'code', 'id');
    }

    public function getCustomerMessageTemplateOptions($value, OrderStatus $record)
    {
        return $this->getAvailableMessageTemplates();
    }

    public function getSystemMessageTemplateOptions($value, OrderStatus $record)
    {
        return $this->getAvailableMessageTemplates();
    }
}
