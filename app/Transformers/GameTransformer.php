<?php

namespace App\Transformers;

use Gate;
use League\Fractal\TransformerAbstract;
use App\Entities\Game;
use App\Services\ResourceUrl;

/**
 * Class GameTransformer.
 */
class GameTransformer extends TransformerAbstract
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
        'tags',
        'shared_with',
        'owner',
    ];

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
            'owner_url'  => $this->urlGen->generate($model->owner),
            'public'     => isset($model->public)     ? (int) $model->public                  : null,
            'jcf'        => isset($model->game)       ? $model->game->jsonSerialize()         : null,
            'created_at' => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
            'updated_at' => isset($model->updated_at) ? $model->updated_at->toIso8601String() : null,
        ], function ($v) {
            return !is_null($v);
        });
    }

    /**
     * Include Tags.
     *
     * @param Game $game
     *
     * @return League\Fractal\CollectionResource
     */
    public function includeTags(Game $game)
    {
        return $this->collection($game->tags, new TagSummaryTransformer());
    }

    /**
     * Include SharedWith.
     *
     * @param Game $game
     *
     * @return League\Fractal\CollectionResource
     */
    public function includeSharedWith(Game $game)
    {
        return $this->collection($game->sharedWith, new UserSummaryTransformer());
    }

    /**
     * Include Owner.
     *
     * @param Game $game
     *
     * @return League\Fractal\ItemResource
     */
    public function includeOwner(Game $game)
    {
        if (Gate::allows('show', $game->owner)) {
            return $this->item($game->owner, new UserTransformer());
        }

        return $this->item($game->owner, new UserSummaryTransformer());
    }
}
