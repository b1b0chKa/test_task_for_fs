<?php

namespace App\Services;

use App\Models\BookingSlots;

class SlotService
{
	/**
	 * Класс проверяет, пересекается ли временной интервал с существующими слотами
	 *
	 * @param  string  $start
	 * @param  string  $end
	 * @param  int|null  $excludeSlotId  ID слота, который нужно исключить (например, при обновлении)
	 * @return bool
	 */
	public function hasOverlap(string $start, string $end, ?int $excludeSlotId = null): bool
	{
		return BookingSlots::where(function ($query) use ($start, $end) {
				$query->whereBetween('start_time', [$start, $end])
					->orWhereBetween('end_time', [$start, $end]);
			})
			->orWhere(function ($query) use ($start, $end)
			{
				$query->where('start_time', '<=', $start)
					->where('end_time', '>=', $end);
			})
			->when($excludeSlotId, function ($query) use ($excludeSlotId)
			{
				$query->where('id', '!=', $excludeSlotId);
			})
			->exists();
	}
}