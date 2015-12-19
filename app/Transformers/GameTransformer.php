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
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tags',
		'shared_with',
		'owner'
    ];


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
			'jcf'        => isset($model->game)       ? $model->game->jsonSerialize()         : null,
            'created_at' => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
            'updated_at' => isset($model->updated_at) ? $model->updated_at->toIso8601String() : null
        ]);
    }

	/**
	 * Include Tags
	 *
	 * @param Game $game
	 * @return League\Fractal\CollectionResource
	 */
	public function includeTags(Game $game)
	{
		return $this->collection($game->tags, new TagTransformer);
	}

	/**
	 * Include SharedWith
	 *
	 * @param Game $game
	 * @return League\Fractal\CollectionResource
	 */
	public function includeSharedWith(Game $game)
	{
		return $this->collection($game->sharedWith, new UserTransformer);
	}

	/**
	 * Include Owner
	 *
	 * @param Game $game
	 * @return League\Fractal\ItemResource
	 */
	public function includeOwner(Game $game)
	{
		return $this->item($game->owner, new UserTransformer);
	}
}
