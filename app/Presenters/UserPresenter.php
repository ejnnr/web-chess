<?php

namespace App\Presenters;

use App\Transformers\UserTransformer;
use App\Presenters\ExtendedFractalPresenter;

/**
 * Class UserPresenter
 *
 * @package namespace App\Presenters;
 */
class UserPresenter extends ExtendedFractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserTransformer();
    }
}
