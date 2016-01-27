<?php

namespace App\Presenters;

use App\Transformers\GameSharedTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class GameSharedPresenter
 *
 * @package namespace App\Presenters;
 */
class GameSharedPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new GameSharedTransformer();
    }
}
