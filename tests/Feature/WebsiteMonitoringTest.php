<?php

namespace Tests\Feature;

use App\Mail\WebsiteDownMail;
use App\Models\Client;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WebsiteMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_clients_api_returns_clients_with_websites(): void
    {
        $client = Client::factory()->create();

        Website::factory()->create([
            'client_id' => $client->id,
            'url' => 'https://example.com',
        ]);

        $response = $this->getJson('/api/clients');

        $response
            ->assertOk()
            ->assertJsonPath('0.email', $client->email)
            ->assertJsonPath('0.websites.0.url', 'https://example.com');
    }

    public function test_down_website_sends_email(): void
    {
        Mail::fake();

        Http::fake([
            'https://example.com' => Http::response('', 500),
        ]);

        $client = Client::factory()->create();

        $website = Website::factory()->create([
            'client_id' => $client->id,
            'url' => 'https://example.com',
        ]);

        $this->artisan('websites:monitor')
            ->assertSuccessful();

        Mail::assertSent(WebsiteDownMail::class, function ($mail) use ($client, $website) {
            return $mail->hasTo($client->email)
                && $mail->websiteUrl === $website->url;
        });
    }

    public function test_up_website_does_not_send_email(): void
    {
        Mail::fake();

        Http::fake([
            'https://example.com' => Http::response('', 200),
        ]);

        $client = Client::factory()->create();

        Website::factory()->create([
            'client_id' => $client->id,
            'url' => 'https://example.com',
        ]);

        $this->artisan('websites:monitor')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }
}