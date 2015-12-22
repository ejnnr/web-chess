<?php

namespace App\Presenters;

use App\Transformers\GameSummaryTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class GameSummaryPresenter
 *
 * @package namespace App\Presenters;
 */
class GameSummaryPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new GameSummaryTransformer();
    }
}
