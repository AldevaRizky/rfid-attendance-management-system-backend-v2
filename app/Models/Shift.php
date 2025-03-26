<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_time', 'end_time', 'grace_period', 'max_late_time'];

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
