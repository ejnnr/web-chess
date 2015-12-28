<?php

namespace App\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\Game;

/**
 * Class GameRepositoryEloquent.
 */
class GameRepositoryEloquent extends ExtendedRepository implements GameRepository
{
    protected $fieldSearchable = [];

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Game::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * return the presenter to use for this repository.
     *
     * @return string
     */
    public function presenter()
    {
        return 'App\Presenters\GamePresenter';
    }
}
