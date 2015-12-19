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
			'database_id'=> isset($model->database_id) ? $model->database_id                  : null,
			'jcf'        => isset($model->game)       ? $model->game->jsonSerialize()         : null,
            'created_at' => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
            'updated_at' => isset($model->updated_at) ? $model->updated_at->toIso8601String() : null
        ]);
    }

	/**
	 * Include Tags
	 *
	 * @param Game $game
	 * @return League\Fractal\ItemResource
	 */
	public function includeDatabase(Game $game)
	{
		return $this->collection($game->tags, new TagTransformer);
	}
}
