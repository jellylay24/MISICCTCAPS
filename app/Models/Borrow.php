<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrow extends Model
{
    /**
     * Default timezone used for displaying dates in Asia/Manila.
     */
    const LOCAL_TZ = 'Asia/Manila';

        protected $fillable = [
        'user_id', 'resource_id', 'quantity', 'duration_minutes', 'status',
        'borrowed_at', 'due_at', 'returned_at', 'notes'
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    protected $appends = [
        'due_at_local',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    // ──────────────────────────────────────────────
    //  Local-timezone helpers
    // ──────────────────────────────────────────────

    /**
     * Return the borrowed_at datetime shifted to Asia/Manila for display.
     * Returns a Carbon instance in the local timezone.
     */
    public function localBorrowedAt(): ?Carbon
    {
        return $this->borrowed_at?->copy()->setTimezone(static::LOCAL_TZ);
    }

    /**
     * Return the due_at datetime shifted to Asia/Manila for display.
     */
    public function localDueAt(): ?Carbon
    {
        return $this->due_at?->copy()->setTimezone(static::LOCAL_TZ);
    }

    /**
     * Return the returned_at datetime shifted to Asia/Manila for display.
     */
    public function localReturnedAt(): ?Carbon
    {
        return $this->returned_at?->copy()->setTimezone(static::LOCAL_TZ);
    }

    /**
     * Accessor: due_at formatted in local timezone, available as $borrow->due_at_local.
     */
    public function getDueAtLocalAttribute(): ?string
    {
        return $this->localDueAt()?->format('M d, Y h:i A');
    }
}
