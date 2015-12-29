<?php namespace App;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Entities\Tag;
use App\Entities\User;

class TagTest extends \TestCase
{
    use DatabaseTransactions;

    public function testIndexTags()
    {
        // make sure there is at least one public tag:
        factory(Tag::class)->create(['public' => 1]);
        $this->json('GET', 'api/tags')
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
                        'id',
                        'name',
                        'owner_id',
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
                        'id',
                        'name',
                        'owner_id',
                        'created_at',
                    ],
                ],
            ]);
        $this->assertResponseOk();
    }

    public function testShowTagUnauthenticated()
    {
        $tagId = factory(Tag::class)->create(['public' => 0])->id;
        $this->json('GET', 'api/tags/'.$tagId);
        $this->assertResponseStatus(401);
    }

    public function testShowTagAuthenticated()
    {
        $user = factory(User::class)->create();
        $tag = Tag::create(['owner_id' => $user->id, 'public' => 1, 'name' => 'new_tag']);
        $this->actingAs($user)
            ->json('GET', 'api/tags/'.$tag->id)
            ->seeJsonStructure([
                'data' => [
                    'id',
                    'owner_id',
                    'name',
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
        $this->json('GET', 'api/tags/999999999999');
        $this->assertResponseStatus(404);
    }

    public function testStoreTag()
    {
        // unauthenticated:

        $this->json('POST', 'api/tags', [
            'data' => [
                'name'   => 'new_tag',
                'public' => 0,
        ], ]);
        $this->assertResponseStatus(401);

        // valid:
        $this->actingAs(User::first());
        $this->json('POST', 'api/tags', [
            'data' => [
                'name'   => 'new_tag',
                'public' => 0,
        ], ]);
        // check if it was actually created and if owner_id was set:
        $this->assertSame(User::first()->id, Tag::find(json_decode($this->getResponse()->content(), true)['data']['id'])->owner_id);

        // invalid name:
        $this->json('POST', 'api/tags', [
            'data' => [
                'name'   => 23,
                'public' => 0,
        ], ]);
        $this->assertResponseStatus(422);

        // non-unique name:
        $this->json('POST', 'api/tags', [
            'data' => [
                'name'   => 'new_tag',
                'public' => 0,
            ],
        ]);
        $this->assertResponseStatus(422);

        // missing name
        $this->json('POST', 'api/tags', [
            'data' => [
                'public' => 0,
            ],
        ]);
        $this->assertResponseStatus(422);

        // missing public attribute
        $this->json('POST', 'api/tags', [
            'data' => [
                'name' => 'new_tag_2',
            ],
        ]);
        $this->assertResponseStatus(422);
    }

    public function testDeleteTag()
    {
        // unauthenticated:
        $tag = factory(Tag::class)->create(['owner_id' => User::first()->id, 'public' => 0]);
        $this->json('DELETE', 'api/tags/'.$tag->id);
        $this->assertResponseStatus(401);

        $this->assertNotNull(Tag::find($tag->id));

        // unauthorized:
        $this->actingAs(factory(User::class)->create())
            ->json('DELETE', 'api/tags/'.$tag->id);
        $this->assertResponseStatus(403);

        $this->assertNotNull(Tag::find($tag->id));

        // valid:
        $this->actingAs(User::first());
        $this->json('DELETE', 'api/tags/'.$tag->id);
        $this->assertResponseStatus(204);

        $this->assertNull(Tag::find($tag->id));

        // non-existing:
        $this->json('DELETE', 'api/tags/'.$tag->id);
        $this->assertResponseStatus(404);
    }

    public function testUpdateTag()
    {
        // unauthenticated:

        $tag = factory(Tag::class)->create(['owner_id' => User::first()->id, 'public' => 1]);
        $this->json('PATCH', 'api/tags/'.$tag->id, [
            'data' => [
                'public' => 3,
            ], ]);
        $this->assertResponseStatus(401);

        // unauthorized:

        $user = factory(User::class)->create();
        $this->actingAs($user)
            ->json('PATCH', 'api/tags/'.$tag->id, [
            'data' => [
                'public' => 3,
            ], ]);
        $this->assertResponseStatus(403);

        // valid:

        $this->actingAs(User::first())
            ->json('PATCH', 'api/tags/'.$tag->id, [
            'data' => [
                'public' => 0,
            ], ])
            ->seeJsonStructure([
                'data' => [
                    'id',
                    'public',
                    'owner_id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ]);
        $this->assertResponseOk();
        $tag = $tag->fresh(); // reload model to see changes
        $this->assertSame(0, $tag->public);

        // non-existing:
        $this->json('PATCH', 'api/tags/999999999999');
        $this->assertResponseStatus(404);

        // invalid name:
        $this->json('PATCH', 'api/tags/'.$tag->id, [
            'data' => [
                'name' => 42,
        ], ]);
        $this->assertResponseStatus(422);

        // invalid public value
        $this->json('PATCH', 'api/tags/'.$tag->id, [
            'data' => [
                'public' => 'hello world',
        ], ]);
        $this->assertResponseStatus(422);

        // can't change owner_id:
        $this->json('PATCH', 'api/tags/'.$tag->id, [
            'data' => [
                'owner_id' => $user->id,
        ], ]);
        $tag = $tag->fresh();
        $this->assertSame(User::first()->id, $tag->owner_id);
    }
}
