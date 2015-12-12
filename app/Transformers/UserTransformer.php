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
        return [
            'id'         => (int) $model->id,
			'name'       => $model->name,
			'email'      => $model->email,
            'created_at' => $model->created_at->toIso8601String(),
            'updated_at' => $model->updated_at->toIso8601String()
        ];
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
