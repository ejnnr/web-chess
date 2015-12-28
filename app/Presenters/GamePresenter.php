<?php

namespace App\Presenters;

use App\Transformers\GameTransformer;

/**
 * Class GamePresenter.
 */
class GamePresenter extends ExtendedFractalPresenter
{
    /**
     * Transformer.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new GameTransformer();
    }
}
