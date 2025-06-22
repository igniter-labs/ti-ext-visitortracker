<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Tests\Models;

use IgniterLabs\VisitorTracker\Classes\RepositoryManager;
use IgniterLabs\VisitorTracker\Models\PageVisit;
use IgniterLabs\VisitorTracker\Models\Settings;
use Illuminate\Support\Carbon;

it('clears all existing page visits', function(): void {
    $this->travelTo(now()->subMonths(6));
    Settings::set('archive_time_out', 2);
    PageVisit::query()->truncate();
    PageVisit::factory()->create(['ip_address' => '127.0.0.1']);
    $this->travelBack();

    resolve(RepositoryManager::class)->clearLog();

    expect(PageVisit::all()->count())->toBe(0);
});

it('returns last online date for a given IP', function(): void {
    $ip = '127.0.0.1';

    PageVisit::factory()->create(['ip_address' => $ip]);

    $result = (new PageVisit)->getLastOnline($ip);

    expect($result->created_at)->toBeInstanceOf(Carbon::class)
        ->and($result->date_added)->toBeString();
});

it('returns online dates for all customers', function(): void {
    $result = (new PageVisit)->getOnlineDates();

    expect($result->toArray())->toBeArray();
});

it('applies is online query scope', function(): void {
    Settings::set('online_time_out', 5);

    $query = (new PageVisit)->isOnline(true);

    expect($query->toSql())->toContain('where `created_at` >= ?');
});

it('prunes records older than configured archive timeout', function(): void {
    $pastMonths = 2;
    Settings::set([
        'status' => true,
        'archive_time_out' => $pastMonths,
    ]);

    $prunableQuery = (new PageVisit)->prunable();

    expect($prunableQuery->toSql())->toContain('where `updated_at` <= ?');
});

it('returns null platform when user agent is missing', function(): void {
    PageVisit::flushEventListeners();
    $pageVisit = PageVisit::factory()->create([
        'user_agent' => '',
        'headers' => [],
    ]);
    $foundPageVisit = PageVisit::find($pageVisit->getKey());

    expect($foundPageVisit->platform)->toBeNull()
        ->and($foundPageVisit->getAgent())->toBeNull();
});

it('returns platform details for a desktop user agent', function(): void {
    PageVisit::flushEventListeners();
    $pageVisit = PageVisit::factory()->create([
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        'headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'],
    ]);
    $foundPageVisit = PageVisit::find($pageVisit->getKey());

    expect($foundPageVisit->platform)->toContain('Computer');
});

it('returns platform details for a tablet user agent', function(): void {
    PageVisit::flushEventListeners();
    $pageVisit = PageVisit::factory()->create([
        'user_agent' => 'Mozilla/5.0 (Linux; Android 9; Nexus 7)',
        'headers' => ['User-Agent' => 'Mozilla/5.0 (Linux; Android 9; Nexus 7)'],
    ]);
    $foundPageVisit = PageVisit::find($pageVisit->getKey());

    expect($foundPageVisit->platform)->toContain('Tablet');
});

it('returns platform details for a mobile phone user agent', function(): void {
    PageVisit::flushEventListeners();
    $pageVisit = PageVisit::factory()->create([
        'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
        'headers' => ['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1'],
    ]);
    $foundPageVisit = PageVisit::find($pageVisit->getKey());

    expect($foundPageVisit->platform)->toContain('iPhone');
});

it('returns platform details for a robot user agent', function(): void {
    PageVisit::flushEventListeners();
    $pageVisit = PageVisit::factory()->create([
        'user_agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)',
        'headers' => ['User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)'],
    ]);
    $foundPageVisit = PageVisit::find($pageVisit->getKey());

    expect($foundPageVisit->platform)->toContain('Robot');
});
