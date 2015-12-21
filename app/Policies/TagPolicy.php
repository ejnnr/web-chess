<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Entities\User;
use App\Entities\Tag;

class TagPolicy
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

	public function update(User $user, Tag $tag)
	{
		if ($tag->public > 2) {
			return true;
		}

		if ($tag->owner_id === $user->id) {
			return true;
		}

		return in_array($user->id, $tag->sharedWith->modelKeys()) && $tag->sharedWith()->where('user_id', '=', $user->id)->first()->pivot->access_level > 2;
	}

	public function destroy(User $user, Tag $tag)
	{
		if ($tag->public > 2) {
			return true;
		}

		if ($tag->owner_id === $user->id) {
			return true;
		}

		return in_array($user->id, $tag->sharedWith->modelKeys()) && $tag->sharedWith()->where('user_id', '=', $user->id)->first()->pivot->access_level > 2;
	}

	public function show(User $user, Tag $tag)
	{
		if ($tag->public > 0) {
			return true;
		}

		if ($tag->owner_id === $user->id) {
			return true;
		}

		return in_array($user->id, $tag->sharedWith->modelKeys()) && $tag->sharedWith()->where('user_id', '=', $user->id)->first()->pivot->access_level > 0;
	}
}
