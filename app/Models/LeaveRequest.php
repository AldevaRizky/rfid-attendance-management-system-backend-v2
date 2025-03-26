<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    // Kolom yang dapat diisi sesuai perubahan tabel
    protected $fillable = ['user_id', 'type', 'start_date', 'end_date', 'reason', 'attachment'];

    /**
     * Relasi ke model User (pemilik izin)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
