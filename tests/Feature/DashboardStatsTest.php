<?php

namespace Tests\Feature;

use App\Filament\Widgets\DashboardStatsOverview;
use App\Models\Benefit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats_include_the_total_benefit_count(): void
    {
        Benefit::create([
            'name' => 'Test Benefit',
            'description' => 'Ein Test-Benefit für die Dashboard-KPI.',
        ]);

        $widget = app(DashboardStatsOverview::class);
        $method = new \ReflectionMethod($widget, 'getStats');
        $method->setAccessible(true);
        $stats = $method->invoke($widget);
        $benefitStat = collect($stats)->first(
            fn ($stat): bool => $stat->getLabel() === 'Benefits gesamt'
        );

        $this->assertNotNull($benefitStat);
        $this->assertSame(1, $benefitStat->getValue());
    }
}
