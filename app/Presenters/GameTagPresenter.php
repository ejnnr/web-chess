<?php

namespace App\Presenters;

use App\Transformers\GameTagTransformer;
use App\Presenters\ExtendedFractalPresenter;

/**
 * Class GameTagPresenter
 *
 * @package namespace App\Presenters;
 */
class GameTagPresenter extends ExtendedFractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new GameTagTransformer();
    }
}
