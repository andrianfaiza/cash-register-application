<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'bahasa', 'mata_uang', 'format_tanggal', 'mode_tampilan', 'notif_email', 'notif_sistem',
    ];

    protected function casts(): array
    {
        return ['notif_email' => 'boolean', 'notif_sistem' => 'boolean'];
    }
}