<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Game;

/**
 * Class GameSummaryTransformer
 * @package namespace App\Transformers;
 */
class GameSummaryTransformer extends TransformerAbstract
{

    /**
     * Transform the \Game entity
     * @param \Game $model
     *
     * @return array
     */
    public function transform(Game $model)
    {
        return array_filter([
            'id'         => isset($model->id)         ? (int) $model->id                      : null,
			'owner_id'   => isset($model->owner_id)   ? $model->owner_id                      : null,
            'created_at' => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
        ]);
    }
}
