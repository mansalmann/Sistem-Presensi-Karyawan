<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\AttendanceResource;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Export to Excel')
            ->color('info')
            ->url(route('attendance-export')),
            Action::make('Attendance Page')
            ->color('success')
            ->url(route('attendance'), shouldOpenInNewTab: true),
            Actions\CreateAction::make(),
        ];
    }
}
