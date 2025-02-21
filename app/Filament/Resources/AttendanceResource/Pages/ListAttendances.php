<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\AttendanceResource;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        $array = [
            Action::make('Attendance Page')
            ->color('success')
            ->url(route('attendance'), shouldOpenInNewTab: true),
            Actions\CreateAction::make()
        ];
        
        if(Auth::user()->hasRole('super_admin')){
            $array[] = Action::make('Export to Excel')
                      ->color('info')
                      ->url(route('attendance-export'));
        }

        return $array;
    }
}
