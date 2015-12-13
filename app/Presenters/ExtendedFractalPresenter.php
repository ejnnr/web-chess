<?php namespace App\Presenters;

use Request;
use Prettus\Repository\Presenter\FractalPresenter;

abstract class ExtendedFractalPresenter extends FractalPresenter
{
    public function parseIncludes($includes = array())
    {
        $request        = app('Illuminate\Http\Request');
        $paramIncludes  = config('repository.fractal.params.include','include');

        if ( $request->has( $paramIncludes ) ) {
            $includes = array_merge($includes, explode(',', Request::input($paramIncludes)));
        }

        $this->fractal->parseIncludes($includes);

        return $this;
  	}
}
