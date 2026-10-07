<?php

namespace App\Filament\Organisation\Pages;

use App\Filament\Organisation\Support\MemberTable;
use App\Models\Member;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Members extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Angemeldete User';

    protected static ?string $title = 'Angemeldete User';

    protected static ?string $slug = 'angemeldete-user';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.organisation.members';

    public function table(Table $table): Table
    {
        return MemberTable::configure($table, Member::query(), allowBlocking: true);
    }
}
