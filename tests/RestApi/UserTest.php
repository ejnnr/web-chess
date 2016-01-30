<?php namespace App;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Entities\User;

class UserTest extends \TestCase
{
    use DatabaseTransactions;

    public function testIndexUsers()
    {
        $this->json('GET', 'api/users')
            ->seeJsonStructure([
                'meta' => [
                    'pagination' => [
                        'count',
                        'total',
                        'per_page',
                        'current_page',
                        'total_pages',
                        'links' => [
                            'next',
                        ],
                    ],
                ],
                'data' => [
                    0 => [
                        'url',
                        'name',
                        'created_at',
                    ],
                ],
            ]);
        $this->assertResponseOk();
        $this->assertRegExp('/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/',
            json_decode($this->getResponse()->content(), true)['data'][0]['created_at']); // assert that created_at is ISO 8601

        $this->json('GET', json_decode($this->getResponse()->content())->meta->pagination->links->next)
            ->seeJsonStructure([
                'meta' => [
                    'pagination' => [
                        'count',
                        'total',
                        'per_page',
                        'current_page',
                        'total_pages',
                        'links' => [
                            'next',
                            'previous',
                        ],
                    ],
                ],
                'data' => [
                    0 => [
                        'url',
                        'name',
                        'created_at',
                    ],
                ],
            ]);
        $this->assertResponseOk();
    }

    public function testShowUserUnauthenticated()
    {
        $this->json('GET', 'api/users/1')
            ->seeJsonStructure([
                'data' => [
                    'url',
                    'name',
                    'created_at',
                ],
            ]);
        $this->assertResponseOk();
        $this->assertRegExp('/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/',
            json_decode($this->getResponse()->content(), true)['data']['created_at']); // assert that created_at is ISO 8601
    }

    public function testShowUserAuthenticated()
    {
        $user = factory(Entities\User::class)->create();
        $this->actingAs($user)
            ->json('GET', 'api/users/'.$user->id)
            ->seeJsonStructure([
                'data' => [
                    'url',
                    'name',
                    'email',
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
        $this->json('GET', 'api/users/999999999999');
        $this->assertResponseStatus(404);
    }

    public function testShowCurrentUser()
    {
        $this->json('GET', 'api/user')
            ->assertResponseStatus(401);
        $this->actingAs(User::first())
            ->json('GET', 'api/user')
            ->seeJsonstructure([
                'data' => [
                    'url',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);
        $this->assertResponseOk();
    }

    public function testUpdateCurrentUser()
    {
        $this->json('PATCH', 'api/user')
            ->assertResponseStatus(401);
        $this->actingAs(User::first())
            ->json('PATCH', 'api/user', [
                'data' => [
                    'name' => 'new_user_name',
                ],
            ])
            ->seeJsonstructure([
                'data' => [
                    'url',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);
        $this->assertResponseOk();
        $this->assertSame('new_user_name', User::first()->name);
    }

    public function testStoreUser()
    {
        $this->json('POST', 'api/users', [
            'data' => [
                'name'     => 'test_user_1',
                'email'    => 'test_user_1@example.org',
                'password' => 'password',
            ], ])
            ->seeJsonStructure([
                'data' => [
                    'url',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);
        $this->assertResponseStatus(201);

        // check if it was actually created:
        $this->json('GET', json_decode($this->getResponse()->content(), true)['data']['url'])
            ->assertResponseOk();

        // check name uniqueness:
        $this->json('POST', 'api/users', [
            'data' => [
                'name'     => 'test_user_1',
                'email'    => 'test_user_2@example.org',
                'password' => 'password',
            ], ]);
        $this->assertResponseStatus(422);

        // check email uniqueness:
        $this->json('POST', 'api/users', [
            'data' => [
                'name'     => 'test_user_2',
                'email'    => 'test_user_1@example.org',
                'password' => 'password',
            ], ]);
        $this->assertResponseStatus(422);

        // check email validation
        $this->json('POST', 'api/users', [
            'data' => [
                'name'     => 'test_user_2',
                'email'    => 'test_user_2@example',
                'password' => 'password',
            ], ]);
        $this->assertResponseStatus(422);

        // check name validation
        $this->json('POST', 'api/users', [
            'data' => [
                'name'     => 'test user 2',
                'email'    => 'test_user_2@example.org',
                'password' => 'password',
            ], ]);
        $this->assertResponseStatus(422);

        // check name required
        $this->json('POST', 'api/users', [
            'data' => [
                'email'    => 'test_user_2@example.org',
                'password' => 'password',
            ], ]);
        $this->assertResponseStatus(422);

        // check email required
        $this->json('POST', 'api/users', [
            'data' => [
                'name'     => 'test_user_2',
                'password' => 'password',
            ], ]);
        $this->assertResponseStatus(422);

        // check password required
        $this->json('POST', 'api/users', [
            'data' => [
                'name'  => 'test_user_2',
                'email' => 'test_user_2@example.org',
            ], ]);
        $this->assertResponseStatus(422);
    }

    public function testDeleteUser()
    {
        // unauthenticated

        $userId = Entities\User::first()->id;
        $this->json('DELETE', 'api/users/'.$userId);
        $this->assertResponseStatus(401);

        $this->assertNotNull(Entities\User::find($userId));

        // valid:

        $userId = factory(User::class)->create()->id;
        $this->actingAs(Entities\User::findOrFail($userId))
            ->json('DELETE', 'api/users/'.$userId);
        $this->assertResponseStatus(204);

        $this->assertNull(Entities\User::find($userId));

        // non-existing:
        $this->json('DELETE', 'api/games/'.$userId);
        $this->assertResponseStatus(404);

        // unauthorized:

        $userId = Entities\User::first()->id;
        $this->json('DELETE', 'api/users/'.$userId);
        $this->assertResponseStatus(403); // still acting as newly created user

        $this->assertNotNull(Entities\User::find($userId));
    }

    public function testUpdateUser()
    {
        // unauthenticated:

        $userId = Entities\User::first()->id;
        $this->json('PATCH', 'api/users/'.$userId, [
            'data' => [
                'name' => 'new_user_name',
            ], ]);
        $this->assertResponseStatus(401);

        // unauthorized:

        $user = factory(Entities\User::class)->create();
        $this->actingAs($user)
            ->json('PATCH', 'api/users/'.$userId, [
            'data' => [
                'name' => 'new_user_name',
            ], ]);
        $this->assertResponseStatus(403);

        // valid:

        $userId = $user->id;
        $this->json('PATCH', 'api/users/'.$userId, [
            'data' => [
                'name'  => 'new_user_name',
                'email' => 'new_email@example.org',
            ], ])
            ->seeJsonStructure([
                'data' => [
                    'url',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);
        $this->seeJson(['name' => 'new_user_name']);
        $this->assertResponseOk();
        $user = $user->fresh(); // reload model to see changes
        $this->assertSame('new_user_name', $user->name);

        // non-existing:
        $this->json('PATCH', 'api/users/999999999999');
        $this->assertResponseStatus(404);

        // check name uniqueness with same name:
        $this->json('PATCH', 'api/users/'.$userId, [
            'data' => [
                'name' => 'new_user_name',
            ], ]);
        $this->assertResponseStatus(200);

        // check email uniqueness with same email:
        $this->json('PATCH', 'api/users/'.$userId, [
            'data' => [
                'email' => 'new_email@example.org',
            ], ]);
        $this->assertResponseStatus(200);

        // check name uniqueness with different name:
        $this->json('PATCH', 'api/users/'.$userId, [
            'data' => [
                'name' => Entities\User::first()->name,
            ], ]);
        $this->assertResponseStatus(422);

        // check email uniqueness with different email:
        $this->json('PATCH', 'api/users/'.$userId, [
            'data' => [
                'email' => Entities\User::first()->email,
            ], ]);
        $this->assertResponseStatus(422);

        // check email validation
        $this->json('PATCH', 'api/users/'.$userId, [
            'data' => [
                'email' => 'new_email@.org',
            ], ]);
        $this->assertResponseStatus(422);

        // check name validation
        $this->json('PATCH', 'api/users/'.$userId, [
            'data' => [
                'name' => 'Hello World',
            ], ]);
        $this->assertResponseStatus(422);
    }
}
