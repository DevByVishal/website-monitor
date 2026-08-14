<?php

namespace App\Console\Commands;

use App\Mail\WebsiteDownMail;
use App\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class MonitorWebsites extends Command
{
    protected $signature = 'websites:monitor';

    protected $description = 'Check all configured websites';

    public function handle(): int
    {
        $websites = Website::with('client')->get();

        foreach ($websites as $website) {

            try {
                $response = Http::timeout(10)
                    ->connectTimeout(10)
                    ->get($website->url);

                if ($response->successful()) {

                    $this->info("UP: {$website->url}");

                } else {

                    $this->error(
                        "DOWN: {$website->url} - HTTP {$response->status()}"
                    );

                    $this->sendDownAlert($website);
                }

            } catch (\Throwable $e) {

                $this->error(
                    "DOWN: {$website->url} - {$e->getMessage()}"
                );

                $this->sendDownAlert($website);
            }
        }

        return self::SUCCESS;
    }

    private function sendDownAlert(Website $website): void
    {
        Mail::to($website->client->email)
            ->send(new WebsiteDownMail($website->url));
    }
}