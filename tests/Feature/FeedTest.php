<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Feed;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Comment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeedTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    protected $user;
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    #[Test]
    public function it_fetches_all_feeds_with_user()
    {
        Feed::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/feeds');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'feeds' => [['id', 'content', 'user_id', 'user']]
                 ]);
    }

    #[Test]
    public function it_creates_a_feed()
    {
        $response = $this->postJson('/api/feed/store', [
            'content' => 'This is a test post'
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'sucess']);

        $this->assertDatabaseHas('feeds', [
            'content' => 'This is a test post',
            'user_id' => $this->user->id
        ]);
    }

    #[Test]
    public function it_likes_a_post_and_then_unlikes_it()
    {
        $feed = Feed::factory()->create(['user_id' => $this->user->id]);

        // Like the post
        $response = $this->postJson("/api/feed/like/{$feed->id}");
        $response->assertStatus(201)->assertJson(['message' => 'liked']);

        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'feed_id' => $feed->id
        ]);

        // Unlike the post
        $response = $this->postJson("/api/feed/like/{$feed->id}");
        $response->assertStatus(200)->assertJson(['message' => 'unliked']);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'feed_id' => $feed->id
        ]);
    }

    #[Test]
    public function it_returns_404_when_liking_a_non_existent_post()
    {
        $response = $this->postJson("/api/feed/like/9999");

        $response->assertStatus(404)
                 ->assertJson(['message' => '404 Not found']);
    }

    #[Test]
    public function it_creates_a_comment_on_a_feed()
    {
        $feed = Feed::factory()->create();

        $response = $this->postJson('/api/feed/comments/create', [
            'body' => 'This is a comment',
            'feed_id' => $feed->id
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'success']);

        $this->assertDatabaseHas('comments', [
            'feed_id' => $feed->id,
            'user_id' => $this->user->id,
            'body' => 'This is a comment',
        ]);
    }

    #[Test]
    public function it_fetches_comments_for_a_feed()
    {
        $feed = Feed::factory()->create();
        Comment::factory()->count(2)->create([
            'feed_id' => $feed->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/feed/comments/{$feed->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'comments' => [['id', 'body', 'user_id', 'feed_id', 'user', 'feed']]
                 ]);
    }
}
