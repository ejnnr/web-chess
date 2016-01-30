<?php

namespace App\Presenters;

use App\Transformers\TagSharedTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class TagSharedPresenter
 *
 * @package namespace App\Presenters;
 */
class TagSharedPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new TagSharedTransformer();
    }
}
