<?php

use App\Livewire\Presensi;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::middleware(['auth'])->group(function(){
    Route::get('/attendance', Presensi::class)->name('attendance');
    Route::get('/attendance/export', function(){
        return Excel::download(new AttendanceExport, 'attendances.xlsx');
    })->name('attendance-export');
});
Route::middleware(['guest'])->group(function(){
    Route::get('/login', function(){
        return redirect('/admin/login');
    })->name('login');
});