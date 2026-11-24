<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'status_baru',
        'catatan',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function aktor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}