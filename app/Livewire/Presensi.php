<?php

namespace App\Livewire;

use App\Models\Leave;
use Livewire\Component;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Presensi extends Component
{

    use LivewireAlert;

    public $latitude;
    public $longitude;
    public $isInsideRadius = false;
    public $schedule;
    public $attendance;

    public function mount(){

        $this->schedule = Schedule::where('user_id', auth()->user()->id)->first();
        $this->attendance = Attendance::where('user_id', auth()->user()->id)->whereDate('created_at', date('Y-m-d'))->first();
        if(!$this->schedule){
            return redirect('/admin/schedules');
        }
    }

    public function render()
    {
        return view('livewire.presensi', [
            'schedule' => $this->schedule,
            'isInsideRadius' => $this->isInsideRadius,
            'attendance' => $this->attendance
        ]);
    }

    public function store(){
        $this->validate([
            'latitude' => ['required'],
            'longitude' => ['required'],
        ]);

        $today = Carbon::today()->format('Y-m-d');
        $approvedLeave = Leave::where('user_id', Auth::user()->id)
                        ->where('status', 'Approved')
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->exists();
                        
        if($approvedLeave){
            $this->alert('error', 'You can\'t submit attendance while you still have days off ', [
                'position' => 'center',
                'toast' => true
            ]);
            return;
        }

        if($this->schedule){
            if(!$this->attendance){
                $this->attendance = Attendance::create([
                    'user_id' => Auth::user()->id,
                    'schedule_latitude' => $this->schedule->office->latitude,
                    'schedule_longitude' => $this->schedule->office->longitude,
                    'schedule_start_time' => $this->schedule->shift->start_time,
                    'schedule_end_time' => $this->schedule->shift->end_time,
                    'start_latitude' => $this->latitude,
                    'start_longitude' => $this->longitude,
                    'end_latitude' => $this->latitude,
                    'end_longitude' => $this->longitude,
                    'start_time' => Carbon::now()->toTimeString(),
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            }else{
                $this->attendance->update([
                    'end_latitude' => $this->latitude,
                    'end_longitude' => $this->longitude,
                    'end_time' => Carbon::now()->toTimeString()
                ]);
            }

            return redirect('/admin/attendances');
            // return redirect()->route('attendance', [
            //     'schedule' => $this->schedule,
            //     'isInsideRadius' => false
            // ]);
        }
        return redirect('/admin/schedules');
    }
}
