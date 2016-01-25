<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Entities\{
    User,
    Game
};

class GamePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function store()
    {
        // false will be returned automatically if no user is logged in, so we can safely assume that the client is authenticated
        return true;
    }

    public function update(User $user, Game $game)
    {
        if ($game->public > 2) {
            return true;
        }

        if ($game->owner_id === $user->id) {
            return true;
        }

        foreach ($game->tags as $tag) {
            if (in_array($user->id, $tag->sharedWith->modelKeys()) && $tag->sharedWith()->where('user_id', '=', $user->id)->first()->pivot->access_level > 2) {
                return true;
            }
        }

        return in_array($user->id, $game->sharedWith->modelKeys()) && $game->sharedWith()->where('user_id', '=', $user->id)->first()->pivot->access_level > 2;
    }

    public function destroy(User $user, Game $game)
    {
        if ($game->public > 2) {
            return true;
        }

        if ($game->owner_id === $user->id) {
            return true;
        }

        foreach ($game->tags as $tag) {
            if (in_array($user->id, $tag->sharedWith->modelKeys()) && $tag->sharedWith()->where('user_id', '=', $user->id)->first()->pivot->access_level > 2) {
                return true;
            }
        }

        return in_array($user->id, $game->sharedWith->modelKeys()) && $game->sharedWith()->where('user_id', '=', $user->id)->first()->pivot->access_level > 2;
    }

    public function show(User $user, Game $game)
    {
        if ($game->public > 0) {
            return true;
        }

        if ($game->owner_id === $user->id) {
            return true;
        }

        foreach ($game->tags as $tag) {
            if (in_array($user->id, $tag->sharedWith->modelKeys()) && $tag->sharedWith()->where('user_id', '=', $user->id)->first()->pivot->access_level > 0) {
                return true;
            }
        }

        return in_array($user->id, $game->sharedWith->modelKeys()) && $game->sharedWith()->where('user_id', '=', $user->id)->first()->pivot->access_level > 0;
    }
}
