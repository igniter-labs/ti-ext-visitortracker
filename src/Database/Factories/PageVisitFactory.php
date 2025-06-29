<?php

declare(strict_types=1);

namespace IgniterLabs\VisitorTracker\Database\Factories;

use Igniter\Flame\Database\Factories\Factory;
use IgniterLabs\VisitorTracker\Models\PageVisit;

class PageVisitFactory extends Factory
{
    protected $model = PageVisit::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'access_type' => 'page',
            'ip_address' => '127.0.0.1',
        ];
    }
}
