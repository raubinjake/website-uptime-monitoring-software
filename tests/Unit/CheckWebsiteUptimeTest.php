<?php

namespace Tests\Unit;

use App\Jobs\CheckWebsiteUptime;
use App\Mail\WebsiteDownMail;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckWebsiteUptimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_check_marks_website_up(): void
    {
        Mail::fake();
        Http::fake(['https://example.com' => Http::response('ok', 200)]);

        $website = Client::query()
            ->create(['email' => 'client@example.com'])
            ->websites()
            ->create(['url' => 'https://example.com']);

        (new CheckWebsiteUptime($website->id))->handle();

        $this->assertSame('up', $website->refresh()->last_status);
        $this->assertSame(200, $website->last_status_code);
        Mail::assertNothingSent();
    }

    public function test_failed_check_marks_website_down_and_sends_mail(): void
    {
        Mail::fake();
        Http::fake(['https://example.com' => Http::response('fail', 500)]);

        $website = Client::query()
            ->create(['email' => 'client@example.com'])
            ->websites()
            ->create(['url' => 'https://example.com']);

        (new CheckWebsiteUptime($website->id))->handle();

        $website->refresh();

        $this->assertSame('down', $website->last_status);
        $this->assertSame(500, $website->last_status_code);
        $this->assertNotNull($website->last_alerted_at);

        Mail::assertSent(WebsiteDownMail::class, function (WebsiteDownMail $mail) use ($website) {
            return $mail->hasTo('client@example.com')
                && $mail->website->is($website)
                && $mail->envelope()->subject === 'https://example.com is down!';
        });
    }
}
