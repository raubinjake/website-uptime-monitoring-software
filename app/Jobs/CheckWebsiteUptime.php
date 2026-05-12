<?php

namespace App\Jobs;

use App\Mail\WebsiteDownMail;
use App\Models\Website;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CheckWebsiteUptime implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public int $websiteId)
    {
    }

    public function handle(): void
    {
        $website = Website::query()->with('client')->find($this->websiteId);

        if (! $website || ! $website->is_active) {
            return;
        }

        $result = $this->check($website->url);

        $website->forceFill([
            'last_status' => $result['is_down'] ? 'down' : 'up',
            'last_status_code' => $result['status_code'],
            'last_error' => $result['error'],
            'last_checked_at' => now(),
        ])->save();

        if ($result['is_down']) {
            Mail::to($website->client->email)->send(new WebsiteDownMail($website));

            $website->forceFill(['last_alerted_at' => now()])->save();
        }
    }

    /**
     * @return array{is_down: bool, status_code: int|null, error: string|null}
     */
    private function check(string $url): array
    {
        try {
            $response = Http::timeout(10)->get($url);

            return [
                'is_down' => $response->failed(),
                'status_code' => $response->status(),
                'error' => $response->failed() ? "HTTP {$response->status()}" : null,
            ];
        } catch (Throwable $exception) {
            return [
                'is_down' => true,
                'status_code' => null,
                'error' => $exception->getMessage(),
            ];
        }
    }
}
