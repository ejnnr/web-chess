<?php

namespace App\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\DatabaseRepository;
use App\Repositories\ExtendedRepository;
use App\Entities\Database;

/**
 * Class DatabaseRepositoryEloquent
 * @package namespace App\Repositories;
 */
class DatabaseRepositoryEloquent extends ExtendedRepository implements DatabaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Database::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

	/**
	 * return the presenter to use for this repository
	 *
	 * @return string
	 */
	public function presenter()
	{
		return 'App\Presenters\DatabasePresenter';
	}
}
