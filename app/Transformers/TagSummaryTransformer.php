<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Tag;

/**
 * Class TagSummaryTransformer.
 */
class TagSummaryTransformer extends TransformerAbstract
{
    /**
     * Transform the \Tag entity.
     *
     * @param \Tag $model
     *
     * @return array
     */
    public function transform(Tag $model)
    {
        return array_filter([
            'id'         => $model->id            ?? null,
            'name'       => $model->name          ?? null,
            'owner_id'   => $model->owner_id      ?? null,
            'created_at' => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
        ]);
    }
}
