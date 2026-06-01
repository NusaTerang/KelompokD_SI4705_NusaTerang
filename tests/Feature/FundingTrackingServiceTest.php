<?php

namespace Tests\Feature;

use App\Events\FundingTargetReached;
use App\Models\Project;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class FundingTrackingServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'nama' => 'Donatur Test',
            'email' => 'donatur@example.com',
            'password' => bcrypt('secret'),
            'role' => 'donatur',
        ]);
    }

    public function test_recalculates_dana_terkumpul_only_from_success_donations()
    {
        $project = Project::create([
            'title' => 'Project A',
            'target_dana' => 100000,
            'funding_status' => 'funding',
        ]);

        // Create success donation
        Donation::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id_donatur,
            'amount' => 30000,
            'status' => 'success',
        ]);

        // Create pending donation
        Donation::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id_donatur,
            'amount' => 20000,
            'status' => 'pending',
        ]);

        // Create failed donation
        Donation::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id_donatur,
            'amount' => 50000,
            'status' => 'failed',
        ]);

        $project->refresh();
        $this->assertEquals(30000, $project->dana_terkumpul);
        $this->assertEquals('funding', $project->funding_status);
    }

    public function test_holds_status_change_to_target_tercapai_if_there_is_a_pending_transaction()
    {
        Log::spy();

        $project = Project::create([
            'title' => 'Project B',
            'target_dana' => 50000,
            'funding_status' => 'funding',
        ]);

        // 1. Create pending donation first
        $pendingDonation = Donation::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id_donatur,
            'amount' => 10000,
            'status' => 'pending',
        ]);

        // 2. Create success donation meeting target
        Donation::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id_donatur,
            'amount' => 50000,
            'status' => 'success',
        ]);

        $project->refresh();
        
        $this->assertEquals('funding', $project->funding_status);
        $this->assertEquals(50000, $project->dana_terkumpul);

        Log::shouldHaveReceived('info')
            ->with(Mockery::pattern('/reached target funding, but has pending/'))
            ->atLeast()->once();

        // 3. Resolve pending donation to failed
        $pendingDonation->status = 'failed';
        $pendingDonation->save();

        $project->refresh();

        // Now that pending has resolved, it should transition to target_tercapai
        $this->assertEquals('target_tercapai', $project->funding_status);
    }

    public function test_updates_status_to_target_tercapai_when_target_met_and_no_pending_transactions()
    {
        Event::fake([FundingTargetReached::class]);

        $project = Project::create([
            'title' => 'Project C',
            'target_dana' => 50000,
            'funding_status' => 'funding',
        ]);

        // Success donation meeting target
        Donation::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id_donatur,
            'amount' => 60000,
            'status' => 'success',
        ]);

        $project->refresh();
        $this->assertEquals('target_tercapai', $project->funding_status);
        $this->assertEquals(60000, $project->dana_terkumpul);

        Event::assertDispatched(FundingTargetReached::class, function ($event) use ($project) {
            return $event->project->id === $project->id;
        });
    }

    public function test_fires_target_reached_event_only_once()
    {
        Event::fake([FundingTargetReached::class]);

        $project = Project::create([
            'title' => 'Project D',
            'target_dana' => 50000,
            'funding_status' => 'funding',
        ]);

        // First donation reaching target
        Donation::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id_donatur,
            'amount' => 50000,
            'status' => 'success',
        ]);

        // Second donation
        Donation::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id_donatur,
            'amount' => 10000,
            'status' => 'success',
        ]);

        Event::assertDispatchedTimes(FundingTargetReached::class, 1);
    }

    public function test_returns_correct_realtime_monitoring_data_api()
    {
        $project = Project::create([
            'title' => 'Project E',
            'target_dana' => 100000,
            'funding_status' => 'funding',
        ]);

        Donation::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id_donatur,
            'amount' => 65000,
            'status' => 'success',
        ]);

        $response = $this->getJson("/api/projects/{$project->id}/funding");

        $response->assertStatus(200)
            ->assertJson([
                'project_id' => $project->id,
                'target_dana' => 100000.0,
                'dana_terkumpul' => 65000.0,
                'progress_percentage' => 65,
                'jumlah_donatur' => 1,
                'funding_status' => 'funding',
            ]);
    }

    public function test_returns_correct_donation_feed_api()
    {
        $project = Project::create([
            'title' => 'Project F',
            'target_dana' => 100000,
            'funding_status' => 'funding',
        ]);

        $donation1 = Donation::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id_donatur,
            'amount' => 30000,
            'status' => 'success',
        ]);

        // Non-success donation
        Donation::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id_donatur,
            'amount' => 20000,
            'status' => 'pending',
        ]);

        $response = $this->getJson("/api/projects/{$project->id}/donation-feed");

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $donation1->id);
    }
}
