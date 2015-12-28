<?php

namespace App\Presenters;

use App\Transformers\UserSummaryTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class UserSummaryPresenter.
 */
class UserSummaryPresenter extends FractalPresenter
{
    /**
     * Transformer.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserSummaryTransformer();
    }
}
