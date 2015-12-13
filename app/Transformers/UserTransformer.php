<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\User;

/**
 * Class UserTransformer
 * @package namespace App\Transformers;
 */
class UserTransformer extends TransformerAbstract
{
	/**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'databases'
    ];

    /**
     * Transform the User entity
     * @param \User $model
     *
     * @return array
     */
    public function transform(User $model)
    {
        return array_filter([
            'id'         => isset($model->id)         ? (int) $model->id                      : null,
			'name'       => isset($model->name)       ? $model->name                          : null,
			'email'      => isset($model->email)      ? $model->email                         : null,
            'created_at' => isset($model->created_at) ? $model->created_at->toIso8601String() : null,
            'updated_at' => isset($model->updated_at) ? $model->updated_at->toIso8601String() : null
        ]);
    }

	/**
	 * Include Databases
	 *
	 * @param User $user
	 * @return League/Fractal/CollectionResource
	 */
	public function includeDatabases(User $user)
	{
		return $this->collection($user->databases, new DatabaseTransformer);
	}
}
