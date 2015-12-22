<?php

namespace App\Presenters;

use App\Transformers\TagSummaryTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class TagSummaryPresenter
 *
 * @package namespace App\Presenters;
 */
class TagSummaryPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new TagSummaryTransformer();
    }
}
