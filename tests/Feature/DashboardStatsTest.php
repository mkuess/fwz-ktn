<?php

namespace Tests\Feature;

use App\Filament\Pages\Neuanmeldungen;
use App\Filament\Widgets\DashboardStatsOverview;
use App\Models\Benefit;
use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_member_count_matches_new_registration_navigation_badge(): void
    {
        foreach ([
            ['pending', 'member', false],
            ['pending', 'member', false],
            ['pending', 'admin', false],
            ['pending', 'org_admin', false],
            ['approved', 'member', false],
            ['rejected', 'member', false],
            ['pending', 'member', true],
        ] as $index => [$status, $role, $deleted]) {
            $member = Member::create([
                'first_name' => 'Test',
                'last_name' => 'Mitglied',
                'email' => 'dashboard-'.$index.'@example.test',
                'status' => $status,
                'role' => $role,
            ]);
            if ($deleted) {
                $member->delete();
            }
        }

        $stat = $this->pendingMemberStat();
        $this->assertSame(2, $stat->getValue());
        $this->assertSame((string) $stat->getValue(), Neuanmeldungen::getNavigationBadge());
    }

    public function test_pending_member_count_is_zero_when_only_other_roles_are_pending(): void
    {
        Member::create([
            'first_name' => 'Test',
            'last_name' => 'Verwaltung',
            'email' => 'dashboard-admin@example.test',
            'status' => 'pending',
            'role' => 'admin',
        ]);
        $this->assertSame(0, $this->pendingMemberStat()->getValue());
        $this->assertNull(Neuanmeldungen::getNavigationBadge());
    }

    private function pendingMemberStat(): Stat
    {
        $method = new \ReflectionMethod(DashboardStatsOverview::class, 'getStats');
        $stats = $method->invoke(app(DashboardStatsOverview::class));

        return collect($stats)->first(fn ($stat): bool => $stat->getLabel() === 'Ausstehende Mitglieder');
    }

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
