<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\User;
use App\Services\ResourceUrl;

/**
 * Class TagSharedTransformer
 * @package namespace App\Transformers;
 */
class TagSharedTransformer extends TransformerAbstract
{
    public function __construct(ResourceUrl $urlGen)
    {
        $this->urlGen = $urlGen;
    }

    /**
     * Transform the User entity
     * @param User $model
     *
     * @return array
     */
    public function transform(User $model)
    {
        return array_filter([
            'url'        => $this->urlGen->generate($model),
            'name'        => isset($model->name)       ? $model->name                          : null,
            'access_level'=> isset($model->pivot->access_level) ? $model->pivot->access_level  : null,
            'created_at'  => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
            'attached_at' => isset($model->pivot->created_at) ? $model->pivot->created_at->toIso8601String() : null,
        ]);
    }
}
