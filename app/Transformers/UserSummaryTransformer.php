<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\User;
use App\Services\ResourceUrl;

/**
 * Class UserSummaryTransformer.
 */
class UserSummaryTransformer extends TransformerAbstract
{
    public function __construct(ResourceUrl $urlGen)
    {
        $this->urlGen = $urlGen;
    }

    /**
     * Transform the \User entity.
     *
     * @param \User $model
     *
     * @return array
     */
    public function transform(User $model)
    {
        return array_filter([
            'url'        => $this->urlGen->generate($model),
            'name'       => isset($model->name)       ? $model->name                          : null,
            'created_at' => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
        ]);
    }
}
