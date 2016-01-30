<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Game;
use App\Services\ResourceUrl;

/**
 * Class GameSummaryTransformer.
 */
class GameSummaryTransformer extends TransformerAbstract
{
    public function __construct(ResourceUrl $urlGen)
    {
        $this->urlGen = $urlGen;
    }

    /**
     * Transform the \Game entity.
     *
     * @param \Game $model
     *
     * @return array
     */
    public function transform(Game $model)
    {
        return array_filter([
            'url'        => $this->urlGen->generate($model),
            'owner_id'   => isset($model->owner_id)   ? $model->owner_id                      : null,
            'created_at' => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
        ]);
    }
}
