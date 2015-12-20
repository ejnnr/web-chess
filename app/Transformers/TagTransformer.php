<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Tag;

/**
 * Class TagTransformer
 * @package namespace App\Transformers;
 */
class TagTransformer extends TransformerAbstract
{
	/**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'owner',
		'games',
		'shared_with',
    ];

    /**
     * Transform the \Tag entity
     * @param \Tag $model
     *
     * @return array
     */
    public function transform(Tag $model)
    {
        return array_filter([
            'id'         => isset($model->id)         ? (int) $model->id                      : null,
			'name'       => isset($model->name)       ? $model->name                          : null,
			'owner_id'   => isset($model->owner_id)   ? $model->owner_id                      : null,
            'created_at' => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
            'updated_at' => isset($model->updated_at) ? $model->updated_at->toIso8601String() : null
        ]);
    }

	/**
	 * Include owner
	 *
	 * @param Tag $tag
	 * @return League/Fractal/ItemResource
	 */
	public function includeOwner(Tag $tag)
	{
		return $this->item($tag->owner, new UserTransformer);
	}

	/**
	 * Include Games
	 *
	 * @param Tag $tag
	 * @return League/Fractal/CollectionResource
	 */
	public function includeGames(Tag $tag)
	{
		return $this->collection($tag->games, new GameTransformer);
	}

	/**
	 * Include SharedWith
	 *
	 * @param Tag $tag
	 * @return League/Fractal/CollectionResource
	 */
	public function includeSharedWith(Tag $tag)
	{
		return $this->collection($tag->sharedWith, new UserTransformer);
	}
}
