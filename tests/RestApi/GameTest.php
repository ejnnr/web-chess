<?php namespace App;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Entities\Game;
use App\Entities\User;
use App\Chess\BCFGame;
use App\Chess\Move;

class GameTest extends \TestCase
{
    use DatabaseTransactions;

    public function testIndexGames()
    {
        $this->json('GET', 'api/games')
            ->seeJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'owner_id',
                        'created_at',
                    ],
                ],
            ]);
        $this->assertResponseOk();
        $this->assertRegExp('/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/',
            json_decode($this->getResponse()->content(), true)['data'][0]['created_at']); // assert that created_at is ISO 8601
    }

    public function testShowGameUnauthenticated()
    {
        $gameId = Game::create(['owner_id' => User::first()->id, 'public' => 0, 'jcf' => app(BCFGame::class)->getJCF()])->id;
        $this->json('GET', 'api/games/'.$gameId);
        $this->assertResponseStatus(401);
    }

    public function testShowGameAuthenticated()
    {
        $user = factory(User::class)->create();
        $game = Game::create(['owner_id' => $user->id, 'public' => 1, 'jcf' => app(BCFGame::class)->doMove(new Move('e2', 'e4'))->getJCF()]);
        $this->actingAs($user)
            ->json('GET', 'api/games/'.$game->id)
            ->seeJsonStructure([
                'data' => [
                    'id',
                    'owner_id',
                    'jcf' => [
                        'meta',
                        'moves' => [
                            0 => [
                                'from',
                                'to',
                            ],
                        ],
                    ],
                    'public',
                    'created_at',
                    'updated_at',
                ],
            ]);
        $this->assertResponseOk();
        $this->assertRegExp('/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/',
            json_decode($this->getResponse()->content(), true)['data']['created_at']); // assert that created_at is ISO 8601
        $this->assertRegExp('/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/',
            json_decode($this->getResponse()->content(), true)['data']['updated_at']); // assert that updated_at is ISO 8601

        // non-existing:
        $this->json('GET', 'api/games/999999999999');
        $this->assertResponseStatus(404);
    }

    public function testStoreGame()
    {
        // unauthenticated:

        $this->json('POST', 'api/games', [
            'data' => [
                'jcf'    => app(BCFGame::class)->doMove(new Move('e2', 'e4'))->jsonSerialize(),
                'public' => 0,
        ], ]);
        $this->assertResponseStatus(401);

        // valid:
        $this->actingAs(User::first());
        $this->json('POST', 'api/games', [
            'data' => [
                'jcf'    => app(BCFGame::class)->doMove(new Move('e2', 'e4'))->jsonSerialize(),
                'public' => 0,
        ], ]);
        // check if it was actually created and if owner_id was set:
        $this->assertSame(User::first()->id, Game::find(json_decode($this->getResponse()->content(), true)['data']['id'])->owner_id);

        // invalid JCF:
        $this->json('POST', 'api/games', [
            'data' => [
                'jcf' => [
                    'meta'  => [],
                    'moves' => [
                        [
                            'from' => 'd2',
                            'to'   => 'd5',
                        ],
                    ],
                ],
                'public' => 0,
        ], ]);
        $this->assertResponseStatus(422);

        // invalid public value
        $this->json('POST', 'api/games', [
            'data' => [
                'jcf'    => app(BCFGame::class)->doMove(new Move('e2', 'e4'))->jsonSerialize(),
                'public' => 'hello world',
        ], ]);
        $this->assertResponseStatus(422);
    }

    public function testDeleteGame()
    {
        // unauthenticated

        $game = Game::create(['owner_id' => User::first()->id, 'public' => 0, 'jcf' => app(BCFGame::class)->getJCF()]);
        $this->json('DELETE', 'api/games/'.$game->id);
        $this->assertResponseStatus(401);

        $this->assertNotNull(Game::find($game->id));

        // valid:

        $this->actingAs(User::first());
        $this->json('DELETE', 'api/games/'.$game->id);
        $this->assertResponseStatus(204);

        $this->assertNull(Game::find($game->id));

        // non-existing:
        $this->json('DELETE', 'api/games/'.$game->id);
        $this->assertResponseStatus(404);

        // unauthorized:

        $game = Game::create(['owner_id' => User::first()->id, 'public' => 0, 'jcf' => app(BCFGame::class)->getJCF()]); // create new game since old one has been deleted
        $this->actingAs(factory(User::class)->create())
            ->json('DELETE', 'api/games/'.$game->id);
        $this->assertResponseStatus(403);

        $this->assertNotNull(Game::find($game->id));
    }

    public function testUpdateGame()
    {
        // unauthenticated:

        $game = Game::create(['owner_id' => User::first()->id, 'public' => 1, 'jcf' => app(BCFGame::class)->getJCF()]);
        $this->json('PATCH', 'api/games/'.$game->id, [
            'data' => [
                'public' => 3,
            ], ]);
        $this->assertResponseStatus(401);

        // unauthorized:

        $user = factory(User::class)->create();
        $this->actingAs($user)
            ->json('PATCH', 'api/games/'.$game->id, [
            'data' => [
                'public' => 3,
            ], ]);
        $this->assertResponseStatus(403);

        // valid:

        $this->actingAs(User::first())
            ->json('PATCH', 'api/games/'.$game->id, [
            'data' => [
                'public' => 0,
            ], ])
            ->seeJsonStructure([
                'data' => [
                    'id',
                    'public',
                    'owner_id',
                    'jcf',
                    'created_at',
                    'updated_at',
                ],
            ]);
        $this->assertResponseOk();
        $game = $game->fresh(); // reload model to see changes
        $this->assertSame(0, $game->public);

        // non-existing:
        $this->json('PATCH', 'api/games/999999999999');
        $this->assertResponseStatus(404);

        // invalid JCF:
        $this->json('PATCH', 'api/games/'.$game->id, [
            'data' => [
                'jcf' => [
                    'meta'  => [],
                    'moves' => [
                        [
                            'from' => 'd2',
                            'to'   => 'd5',
                        ],
                    ],
                ],
        ], ]);
        $this->assertResponseStatus(422);

        // invalid public value
        $this->json('PATCH', 'api/games/'.$game->id, [
            'data' => [
                'public' => 'hello world',
        ], ]);
        $this->assertResponseStatus(422);

        // can't change owner_id:
        $this->json('PATCH', 'api/games/'.$game->id, [
            'data' => [
                'owner_id' => $user->id,
        ], ]);
        $game = $game->fresh();
        $this->assertSame(User::first()->id, $game->owner_id);
    }
}
