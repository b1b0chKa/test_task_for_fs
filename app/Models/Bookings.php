<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
	protected $fillable = [
		'user_id',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function slots()
	{
		return $this->hasMany(BookingSlots::class);
	}
}
