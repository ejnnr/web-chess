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
		return $tag->owner_id === $user->id;
	}

	public function destroy(User $user, Tag $tag)
	{
		return $tag->owner_id === $user->id;
	}

	public function show(User $user, Tag $tag)
	{
		return $tag->owner_id === $user->id;
	}
}
