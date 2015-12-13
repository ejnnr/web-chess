<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Database;

/**
 * Class DatabaseTransformer
 * @package namespace App\Transformers;
 */
class DatabaseTransformer extends TransformerAbstract
{
	/**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'owner',
		'games'
    ];

    /**
     * Transform the \Database entity
     * @param \Database $model
     *
     * @return array
     */
    public function transform(Database $model)
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
	 * @param Database $database
	 * @return League/Fractal/ItemResource
	 */
	public function includeOwner(Database $database)
	{
		return $this->item($database->owner, new UserTransformer);
	}

	/**
	 * Include Games
	 *
	 * @param Database $database
	 * @return League/Fractal/CollectionResource
	 */
	public function includeGames(Database $database)
	{
		return $this->collection($database->games, new GameTransformer);
	}
}
