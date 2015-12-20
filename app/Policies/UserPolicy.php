<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Entities\User;

class UserPolicy
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

	public function update(User $authenticatedUser, User $user)
	{
		return $user->id === $authenticatedUser->id;
	}

	public function destroy(User $authenticatedUser, User $user)
	{
		return $user->id === $authenticatedUser->id;
	}

	public function show(User $authenticatedUser, User $user)
	{
		return $user->id === $authenticatedUser->id;
	}
}
