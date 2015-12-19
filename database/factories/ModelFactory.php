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

$factory->define(App\Entities\Database::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->userName,
		// attach the database to a random user:
		'owner_id' => App\Entities\User::orderByRaw("RAND()")->first()->id,
		'public' => rand(0,1)
    ];
});

$factory->define(App\Entities\Database::class, function (Faker\Generator $faker, App\Chess\BCFGame $game) {
	$game->doMove(new App\Chess\Move('e2', 'e4'));

    return [
		// attach the game to a random database:
		'database_id' => App\Entities\Database::orderByRaw("RAND()")->first()->id,
		'bcf' => $game->getBCF()
    ];
});
