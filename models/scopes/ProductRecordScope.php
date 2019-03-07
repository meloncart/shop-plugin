<?php namespace MelonCart\Shop\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ScopeInterface;

class ProductRecordScope implements ScopeInterface
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $model = $builder->getModel();
        $builder->with('baseOptions');
    }

    /**
     * Remove the scope from the given Eloquent query builder.
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function remove(Builder $builder, Model $model)
    {

    }
}
