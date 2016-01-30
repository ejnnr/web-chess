<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Tag;
use App\Services\ResourceUrl;

/**
 * Class TagSummaryTransformer.
 */
class TagSummaryTransformer extends TransformerAbstract
{
    public function __construct(ResourceUrl $urlGen)
    {
        $this->urlGen = $urlGen;
    }

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
            'url'        => $this->urlGen->generate($model),
            'name'       => isset($model->name)       ? $model->name                          : null,
            'owner_url'  => $this->urlGen->generate($model->owner),
            'created_at' => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
        ]);
    }
}
