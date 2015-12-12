<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Game;

/**
 * Class GameTransformer
 * @package namespace App\Transformers;
 */
class GameTransformer extends TransformerAbstract
{

    /**
     * Transform the \Game entity
     * @param \Game $model
     *
     * @return array
     */
    public function transform(Game $model)
    {
        return [
            'id'         => (int) $model->id,
			'database_id'=> $model->database_id,
			'jcf'        => $model->game->getJCF(),
            'created_at' => $model->created_at->toIso8601String(),
            'updated_at' => $model->updated_at->toIso8601String()
        ];
    }
}
