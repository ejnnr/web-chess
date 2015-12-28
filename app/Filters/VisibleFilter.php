<?php namespace App\Filters;

use Auth;

class VisibleFilter
{
    public function apply($collection)
    {
        if (!Auth::check()) {
            $collection = $collection->filter(function ($item) {
                return $item->public;
            });

            return $collection;
        }
        $collection = $collection->filter(function ($item) {
            return Auth::user()->can('show', $item);
        });

        return $collection;
    }
}
