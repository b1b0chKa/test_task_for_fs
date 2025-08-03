<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSlotRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateSlotRequest;
use App\Http\Resources\BookingResource;
use App\Models\Bookings;
use App\Models\BookingSlots;
use App\Services\SlotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
	public function index()
	{
		$bookings = Bookings::where('user_id', Auth::id())
			->with('slots')
			->latest()
			->get();

		return response()->json(BookingResource::collection($bookings));
	}

	public function store(StoreBookingRequest $request, SlotService $slotService) 
	{
		foreach ($request->slots as $slot)
		{
			if ($slotService->hasOverlap($slot['start_time'], $slot['end_time']))
			{
				return response()->json([
					'message' => "Слот {$slot['start_time']} - {$slot['end_time']} уже занят."
				], 422);
			}
		}

		$booking = Auth::user()->bookings()->create();
		foreach ($request->slots as $slot)
			$booking->slots()->create($slot);

		return response()->json(new BookingResource($booking), 201);
	}

	public function addSlot(AddSlotRequest $request, string $bookingId, SlotService $slotService)
	{
		$booking = Bookings::find($bookingId);

		if (!$booking || $booking->user_id !== Auth::id())
			return response()->json(['message' => 'Бронирование не найдено'], 404);

		$slotData = $request->slot;

		if ($slotService->hasOverlap($slotData['start_time'], $slotData['end_time']))
			return response()->json(['message' => 'Время уже занято другим бронированием.'], 422);

		$booking->slots()->create($slotData);

		return response()->json(
			new BookingResource($booking->fresh('slots')),
			201
		);
	}

	public function updateSlot(UpdateSlotRequest $request, string $bookingId, string $slotId, SlotService $slotService)
	{
		$booking = Bookings::find($bookingId);

		if (!$booking || $booking->user_id !== Auth::id())
			return response()->json(['message' => 'Бронирование не найдено'], 404);

		$slot = BookingSlots::find($slotId);

		if (!$slot)
			return response()->json(['message' => 'Слот не найден'], 404);

		if ($slot->booking_id !== $booking->id)
			return response()->json(['message' => 'Слот не принадлежит этой брони'], 404);

		$slotData = $request->slot;

		if ($slotService->hasOverlap($slotData['start_time'], $slotData['end_time'], $slot->id))
			return response()->json(['message' => 'Время уже занято'], 422);

		$slot->update($slotData);

		return response()->json(
			new BookingResource($booking->fresh('slots')),
			200
		);
	}

	public function destroy(string $bookingId)
	{
		$booking = Bookings::find($bookingId);

		if (!$booking || $booking->user_id !== Auth::id())
			return response()->json(['message' => 'Бронирование не найдено'], 404);

		$booking->delete();

		return response()->json([
			'message' => 'Бронирование удалено.'
		], 200);
	}
}
