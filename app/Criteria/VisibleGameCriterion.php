<?php namespace App\Criteria;

use Auth;
use DB;
use Prettus\Repository\Contracts\{
    CriteriaInterface,
    RepositoryInterface
};

/**
 * Class VisibleGameCriterion
 */
class VisibleGameCriterion implements CriteriaInterface
{
    /**
     * Apply criterion in query repository
     *
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if (Auth::check()) {
            $userId = Auth::user()->id;
        } else {
            $userId = 0;
        }
        $model = $model->where(function($query) use ($userId) {
            $query
                ->where('owner_id', '=', $userId)
                ->orWhereExists(function($query) use ($userId) {
                    $query->select(DB::raw(1))
                        ->from('shared_games')
                        ->whereRaw('game_id = games.id')
                        ->where('user_id', '=', $userId)
                        ->where('access_level', '>', 0);
                })
                ->orWhere('public', '>', 0)
                ->orWhereExists(function($query) use ($userId) {
                    $query->select(DB::raw(1))
                        ->from('game_tag')
                        ->whereRaw('game_id = games.id')
                        ->whereExists(function($query) use ($userId) {
                            $query->select(DB::raw(1))
                                ->from('shared_tags')
                                ->where('user_id', '=', $userId)
                                ->whereRaw('tag_id = game_tag.tag_id');
                        });
                });
        });
        return $model;
    }
}
