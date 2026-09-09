<?php

namespace App\Filament\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use App\Services\MemberAccessInvitationService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class Neuanmeldungen extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Neuanmeldungen';

    protected static ?string $title = 'Neuanmeldungen';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.neuanmeldungen';

    public static function getNavigationBadge(): ?string
    {
        $count = Member::where('status', 'pending')->where('role', 'member')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Member::query()
                    ->where('status', 'pending')
                    ->where('role', 'member')
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Mitglied')
                    ->state(fn (Member $record): string => trim(($record->first_name ?? '').' '.($record->last_name ?? '')) ?: '-')
                    ->description(fn (Member $record): string => $record->email ?? '')
                    ->searchable(['first_name', 'last_name', 'email']),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Angemeldet')
                    ->since()
                    ->tooltip(fn (Member $record): string => $record->created_at?->format('d.m.Y H:i') ?? '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('organisation.name')
                    ->label('Organisation')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('address')
                    ->label('Adresse')
                    ->state(function (Member $record): ?string {
                        $street = trim((string) $record->street);
                        $city = trim(implode(' ', array_filter([
                            $record->zip,
                            $record->city,
                        ])));
                        $address = implode("\n", array_filter([$street, $city]));

                        return $address !== '' ? $address : null;
                    })
                    ->placeholder('-')
                    ->wrap(),
            ])
            ->recordUrl(fn (Member $record): string => MemberResource::getUrl('edit', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approveAndSendAccess')
                        ->label('Freischalten und Zugangsdaten senden')
                        ->icon('heroicon-o-envelope')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Mitglieder freischalten')
                        ->modalDescription('Alle ausgewählten Mitglieder werden freigeschaltet und erhalten anschließend ihren sechsstelligen, sieben Tage gültigen Freischaltcode per E-Mail.')
                        ->action(function (Collection $records): void {
                            $sent = 0;
                            $failed = 0;
                            $invitationService = app(MemberAccessInvitationService::class);

                            foreach ($records as $member) {
                                $member->update([
                                    'status' => 'approved',
                                    'approved_at' => now(),
                                ]);

                                try {
                                    $invitationService->send($member);
                                    $sent++;
                                } catch (\Throwable $exception) {
                                    report($exception);
                                    $failed++;
                                }
                            }

                            if ($failed === 0) {
                                Notification::make()
                                    ->title('Mitglieder freigeschaltet')
                                    ->body($sent.' Zugangsdaten-E-Mail(s) wurden versendet.')
                                    ->success()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('Freischaltung abgeschlossen')
                                ->body($sent.' E-Mail(s) versendet, '.$failed.' E-Mail(s) fehlgeschlagen. Die betreffenden Mitglieder wurden trotzdem gespeichert und freigeschaltet.')
                                ->warning()
                                ->persistent()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 30, 50]);
    }
}
