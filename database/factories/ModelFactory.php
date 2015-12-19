<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Entities\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->userName,
        'email' => $faker->unique()->email,
        'password' => $faker->password
    ];
});

$factory->define(App\Entities\Tag::class, function (Faker\Generator $faker) {
	$ids = App\Entities\User::all(['id'])->modelKeys();
    return [
        'name' => $faker->userName,
		// attach the tag to a random user
		'owner_id' => $ids[mt_rand(0, count($ids) - 1)],
		'public' => rand(0,1)
    ];
});

$factory->define(App\Entities\Game::class, function (Faker\Generator $faker) {
	$game = app(App\Chess\BCFGame::class);
	$game->doMove(new App\Chess\Move('e2', 'e4'));
	$ids = App\Entities\User::all(['id'])->modelKeys();

    return [
		'owner_id' => $ids[mt_rand(0, count($ids) - 1)],
		'bcf' => $game->getBCF()
    ];
});
