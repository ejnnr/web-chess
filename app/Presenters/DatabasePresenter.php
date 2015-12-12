<?php

namespace App\Presenters;

use App\Transformers\DatabaseTransformer;
use App\Presenters\ExtendedFractalPresenter;

/**
 * Class DatabasePresenter
 *
 * @package namespace App\Presenters;
 */
class DatabasePresenter extends ExtendedFractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new DatabaseTransformer();
    }
}
