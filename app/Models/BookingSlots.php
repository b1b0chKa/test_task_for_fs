<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSlots extends Model
{
    protected $fillable = [
        'booking_id',
        'start_time',
        'end_time',
    ];

    public function booking()
    {
        return $this->belongsTo(Bookings::class);
    }
}
