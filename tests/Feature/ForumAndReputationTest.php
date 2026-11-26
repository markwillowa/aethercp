<?php

namespace Tests\Feature;

use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Models\ForumBoard;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\ForumUserProfile;
use App\Models\ReputationAuditLog;
use App\Models\ReputationLevel;
use App\Models\ReputationTransaction;
use App\Models\User;
use App\Services\Forum\ForumService;
use App\Services\Forum\ReputationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ForumAndReputationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $playerA;
    protected User $playerB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => bcrypt('Password123!'),
            'role' => 'admin',
            'account_id' => 2000000,
        ]);

        $this->playerA = User::create([
            'name' => 'Player A',
            'username' => 'playera',
            'email' => 'playera_secret@example.com',
            'password' => bcrypt('Password123!'),
            'role' => 'user',
            'account_id' => 2000010,
        ]);

        $this->playerB = User::create([
            'name' => 'Player B',
            'username' => 'playerb',
            'email' => 'playerb_secret@example.com',
            'password' => bcrypt('Password123!'),
            'role' => 'user',
            'account_id' => 2000020,
        ]);

        app(ForumService::class)->seedDefaultsIfEmpty();
    }

    public function test_forum_homepage_and_board_listing_load(): void
    {
        $res = $this->get('/forum');
        $res->assertStatus(200);
        $res->assertSee('General Discussion');

        $boardRes = $this->get('/forum/board/general-discussion');
        $boardRes->assertStatus(200);
        $boardRes->assertSee('General Discussion');
    }

    public function test_topic_creation_permissions_and_reply_creation(): void
    {
        $general = ForumBoard::where('slug', 'general-discussion')->firstOrFail();
        $announcements = ForumBoard::where('slug', 'announcements')->firstOrFail();

        // Player cannot post in staff-only announcements board
        $this->actingAs($this->playerA)
            ->post('/forum/topics', [
                'board_id' => $announcements->id,
                'title' => 'Unauthorized Announcement',
                'content' => 'Trying to post in announcements.',
            ])
            ->assertStatus(403);

        // Player can post in General Discussion
        $createRes = $this->actingAs($this->playerA)
            ->post('/forum/topics', [
                'board_id' => $general->id,
                'title' => 'Best MVP Build for Rune Knight',
                'content' => 'Looking for advice on dragon breath gear.',
            ]);

        $topic = ForumTopic::where('title', 'Best MVP Build for Rune Knight')->firstOrFail();
        $createRes->assertRedirect('/forum/topic/' . $topic->slug);

        // Player B replies
        $this->actingAs($this->playerB)
            ->post('/forum/topic/' . $topic->slug . '/reply', [
                'content' => 'Try using +9 Fallen Angel Wings with Raydric Card.',
            ])
            ->assertRedirect();

        $this->assertEquals(1, $topic->fresh()->replies_count);
    }

    public function test_topic_and_post_editing_permissions(): void
    {
        $general = ForumBoard::where('slug', 'general-discussion')->firstOrFail();
        $topic = app(ForumService::class)->createTopic($this->playerA, $general, [
            'title' => 'Original Title',
            'content' => 'Original content body.',
        ]);
        $firstPost = $topic->firstPost;

        // Player B cannot edit Player A's post
        $this->actingAs($this->playerB)
            ->put('/forum/posts/' . $firstPost->id, [
                'title' => 'Hacked Title',
                'content' => 'Hacked content.',
            ])
            ->assertStatus(403);

        // Player A can edit own topic & post
        $this->actingAs($this->playerA)
            ->put('/forum/posts/' . $firstPost->id, [
                'title' => 'Updated Title',
                'content' => 'Updated content body.',
            ])
            ->assertRedirect();

        $this->assertEquals('Updated Title', $topic->fresh()->title);
        $this->assertNotNull($firstPost->fresh()->edited_at);
    }

    public function test_optional_character_identity_and_ownership_verification(): void
    {
        $accountId = (int) ($this->playerA->account_id ?: 2000010);
        if (!$this->playerA->account_id) {
            $this->playerA->forceFill(['account_id' => $accountId])->saveQuietly();
        }

        $mockRepo = Mockery::mock(CharacterRepositoryInterface::class);
        $mockRepo->shouldReceive('getByAccountId')
            ->with($accountId)
            ->andReturn([
                [
                    'char_id' => 150100,
                    'account_id' => $accountId,
                    'name' => 'AetherKnight',
                    'class' => 4054,
                    'base_level' => 175,
                    'job_level' => 60,
                    'sex' => 'M',
                ],
            ]);
        $this->app->instance(CharacterRepositoryInterface::class, $mockRepo);

        // Selecting a character owned by another player must fail
        $this->actingAs($this->playerA)
            ->post('/forum/settings/identity', [
                'identity_mode' => 'character',
                'selected_char_id' => 999999,
            ])
            ->assertSessionHas('error');

        $profile = ForumUserProfile::where('user_id', $this->playerA->id)->first();
        $this->assertEquals('username', $profile->identity_mode);

        // Selecting owned character succeeds
        $this->actingAs($this->playerA)
            ->post('/forum/settings/identity', [
                'identity_mode' => 'character',
                'selected_char_id' => 150100,
            ])
            ->assertSessionHas('success');

        $profile->refresh();
        $this->assertEquals('character', $profile->identity_mode);
        $this->assertEquals(150100, $profile->selected_char_id);
    }

    public function test_reputation_award_duplicate_prevention_and_reaction_reversal(): void
    {
        $general = ForumBoard::where('slug', 'general-discussion')->firstOrFail();
        $topic = app(ForumService::class)->createTopic($this->playerA, $general, [
            'title' => 'Helpful Guide Topic',
            'content' => 'Detailed leveling guide content.',
        ]);
        $post = $topic->firstPost;

        // Self-like is blocked
        $this->actingAs($this->playerA)
            ->post('/forum/posts/' . $post->id . '/react')
            ->assertSessionHas('error');

        // Player B likes Player A's post -> +1 reputation
        $this->actingAs($this->playerB)
            ->post('/forum/posts/' . $post->id . '/react');

        $repService = app(ReputationService::class);
        $this->assertEquals(1, $repService->getUserTotal($this->playerA->id));

        // Duplicate award with same reference is prevented
        $dup = $repService->award(
            $this->playerA,
            $this->playerB,
            ReputationService::EVENT_POSITIVE_REACTION,
            "reaction:{$post->id}:{$this->playerB->id}:like",
            'Duplicate test',
            $topic->id,
            $post->id
        );
        $this->assertNull($dup);
        $this->assertEquals(1, $repService->getUserTotal($this->playerA->id));

        // Removing reaction reverses the +1 award -> 0 reputation
        $this->actingAs($this->playerB)
            ->post('/forum/posts/' . $post->id . '/react');

        $this->assertEquals(0, $repService->getUserTotal($this->playerA->id));

        // Toggling back on awards +1 (net 1, never 2)
        $this->actingAs($this->playerB)
            ->post('/forum/posts/' . $post->id . '/react');

        $this->assertEquals(1, $repService->getUserTotal($this->playerA->id));
    }

    public function test_reputation_level_calculation_and_leaderboard_filtering(): void
    {
        $repService = app(ReputationService::class);

        $this->assertEquals('Newcomer', $repService->getLevelForPoints(0)['name']);
        $this->assertEquals('Contributor', $repService->getLevelForPoints(10)['name']);
        $this->assertEquals('Active Member', $repService->getLevelForPoints(50)['name']);
        $this->assertEquals('Veteran', $repService->getLevelForPoints(150)['name']);
        $this->assertEquals('Elite Member', $repService->getLevelForPoints(400)['name']);
        $this->assertEquals('Community Legend', $repService->getLevelForPoints(1000)['name']);

        $repService->manualAdjust($this->admin, $this->playerA, 65, 'Great guide contribution');

        $this->get('/forum/leaderboard?period=all')
            ->assertStatus(200)
            ->assertSee('playera')
            ->assertSee('Active Member');

        $this->get('/forum/leaderboard?period=month')->assertStatus(200);
        $this->get('/forum/leaderboard?period=week')->assertStatus(200);
    }

    public function test_admin_permissions_reputation_history_and_manual_adjustment(): void
    {
        // Regular player cannot access admin forum routes
        $this->actingAs($this->playerA)
            ->get('/admin/forum/reputation')
            ->assertStatus(403);

        // Admin can perform manual adjustment with mandatory reason
        $this->actingAs($this->admin)
            ->post('/admin/forum/reputation/adjust', [
                'recipient_user_id' => $this->playerB->id,
                'points_delta' => 25,
                'reason' => 'Community event winner',
            ])
            ->assertSessionHas('success');

        $this->assertEquals(25, app(ReputationService::class)->getUserTotal($this->playerB->id));
        $this->assertDatabaseHas('reputation_audit_logs', [
            'admin_user_id' => $this->admin->id,
            'recipient_user_id' => $this->playerB->id,
            'points_delta' => 25,
            'reason' => 'Community event winner',
        ]);

        $this->actingAs($this->admin)
            ->get('/admin/forum/reputation/history')
            ->assertStatus(200)
            ->assertSee('Community event winner');
    }

    public function test_profile_privacy_xss_prevention_and_upload_validation(): void
    {
        // Profile must never expose email or password
        $profileRes = $this->get('/forum/members/playera');
        $profileRes->assertStatus(200);
        $profileRes->assertDontSee('playera_secret@example.com');

        // XSS payload in topic content is sanitized/escaped
        $general = ForumBoard::where('slug', 'general-discussion')->firstOrFail();
        $this->actingAs($this->playerA)
            ->post('/forum/topics', [
                'board_id' => $general->id,
                'title' => 'XSS Check Topic',
                'content' => 'Hello <script>alert("xss")</script> world!',
            ]);

        $topic = ForumTopic::where('title', 'XSS Check Topic')->firstOrFail();
        $topicRes = $this->get('/forum/topic/' . $topic->slug);
        $topicRes->assertStatus(200);
        $topicRes->assertDontSee('<script>alert("xss")</script>', false);

        // Upload validation rejects .php files
        Storage::fake('local');
        $badFile = UploadedFile::fake()->create('exploit.php', 10);
        $this->actingAs($this->playerA)
            ->post('/forum/topic/' . $topic->slug . '/reply', [
                'content' => 'Reply with bad attachment',
                'attachments' => [$badFile],
            ])
            ->assertSessionHasErrors('attachments.0');
    }
}
