<?php

namespace App\Transformers;

use Gate;
use League\Fractal\TransformerAbstract;
use App\Entities\Tag;
use App\Services\ResourceUrl;

/**
 * Class GameTagTransformer.
 */
class GameTagTransformer extends TransformerAbstract
{
    public function __construct(ResourceUrl $urlGen)
    {
        $this->urlGen = $urlGen;
    }

    /**
     * List of resources possible to include.
     *
     * @var array
     */
    protected $availableIncludes = [
        'owner',
    ];

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
            'name'        => isset($model->name)       ? $model->name                          : null,
            'owner_url'  => $this->urlGen->generate($model->owner),
            'created_at'  => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
            'attached_at' => isset($model->pivot->created_at) ? $model->pivot->created_at->toIso8601String() : null,
        ]);
    }

    /**
     * Include owner.
     *
     * @param Tag $tag
     *
     * @return League/Fractal/ItemResource
     */
    public function includeOwner(Tag $tag)
    {
        if (Gate::allows('show', $tag->owner)) {
            return $this->item($tag->owner, new UserTransformer());
        }

        return $this->item($tag->owner, new UserSummaryTransformer());
    }
}
