<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\User;

/**
 * Class GameSharedTransformer
 * @package namespace App\Transformers;
 */
class GameSharedTransformer extends TransformerAbstract
{

    /**
     * Transform the User entity
     * @param User $model
     *
     * @return array
     */
    public function transform(User $model)
    {
        return array_filter([
            'id'          => isset($model->id)         ? (int) $model->id                      : null,
            'name'        => isset($model->name)       ? $model->name                          : null,
            'access_level'=> isset($model->pivot->access_level) ? $model->pivot->access_level  : null,
            'created_at'  => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
            'attached_at' => isset($model->pivot->created_at) ? $model->pivot->created_at->toIso8601String() : null,
        ]);
    }
}
