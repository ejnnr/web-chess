<?php namespace App\Criteria;

use Auth;
use DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class VisibleTagCriterion
 */
class VisibleTagCriterion implements CriteriaInterface
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
        $model = $model
            ->where('owner_id', '=', $userId)
            ->orWhereExists(function($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('shared_tags')
                    ->whereRaw('tag_id = tags.id')
                    ->where('user_id', '=', $userId)
                    ->where('access_level', '>', 0);
            })
            ->orWhere('public', '>', 0);
        return $model;
    }
}
