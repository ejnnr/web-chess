<?php

namespace App\Presenters;

use App\Transformers\GameTagTransformer;

/**
 * Class GameTagPresenter.
 */
class GameTagPresenter extends ExtendedFractalPresenter
{
    /**
     * Transformer.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new GameTagTransformer();
    }
}
