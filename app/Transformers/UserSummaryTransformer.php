<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\User;

/**
 * Class UserSummaryTransformer
 * @package namespace App\Transformers;
 */
class UserSummaryTransformer extends TransformerAbstract
{

    /**
     * Transform the \User entity
     * @param \User $model
     *
     * @return array
     */
    public function transform(User $model)
    {
        return array_filter([
            'id'         => isset($model->id)         ? (int) $model->id                      : null,
			'name'       => isset($model->name)       ? $model->name                          : null,
            'created_at' => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
        ]);
    }
}
