<?php namespace App\Filters;

use Auth;

class VisibleFilter
{
    public function apply($collection)
    {
        $collection = $collection->filter(function($item) {
			return Auth::user()->can('show', $item);
		});
			
        return $collection;
    }
}
