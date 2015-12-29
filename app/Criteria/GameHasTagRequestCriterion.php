<?php namespace App\Criteria;

use Auth;
use DB;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class GameHasTagRequestCriterion.
 */
class GameHasTagRequestCriterion implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criterion in query repository.
     *
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if (empty($this->request->get('tag'))) {
            return $model;
        }

        $tags = explode(';', $this->request->get('tag'));

        foreach ($tags as $tag) {
            $model = $model->whereExists(function($query) use ($tag) {
                $query->select(DB::raw(1))
                    ->from('game_tag')
                    ->where('tag_id', '=', $tag)
                    ->whereRaw('game_id = games.id');
            });
        }
        return $model;
    }
}
