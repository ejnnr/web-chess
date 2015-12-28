<?php

namespace App\Presenters;

use App\Transformers\GameSummaryTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class GameSummaryPresenter.
 */
class GameSummaryPresenter extends FractalPresenter
{
    /**
     * Transformer.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new GameSummaryTransformer();
    }
}
