<?php

namespace App\Presenters;

use App\Transformers\UserTransformer;

/**
 * Class UserPresenter.
 */
class UserPresenter extends ExtendedFractalPresenter
{
    /**
     * Transformer.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserTransformer();
    }
}
