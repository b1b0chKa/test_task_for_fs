<?php

namespace Tests\Feature;

use App\Models\Bookings;
use App\Models\BookingSlots;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
	use RefreshDatabase;

	protected $user;
	protected $token;
	/**
	 * A basic feature t
	 * est example.
	 */
	public function setUp(): void
	{
		parent::setUp();

		$this->user  = User::factory()->create();
		$this->token = $this->user->api_token;
	}

	/**
	 * Создание бронирования с несколькими слотами
	 */
	public function testAddSeveralSlots()
	{
		$response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
			->postJson('/api/bookings', [
				'slots' => [
					[
						'start_time' => '2025-06-25T12:00:00',
						'end_time'   => '2025-06-25T13:00:00',
					],
					[
						'start_time' => '2025-06-25T13:30:00',
						'end_time'   => '2025-06-25T14:30:00',
					],
				]
			]);

		$response->assertStatus(201);
        $response->assertJsonStructure(['id', 'user', 'created_at']);

		$this->assertDatabaseCount('bookings', 1);
		$this->assertDatabaseCount('booking_slots', 2);
	}

	/**
	 * Добавление слота с конфликтом
	 */
	public function testTimeConflict()
	{
		$booking = Bookings::create([
			'user_id' => $this->user->id
		]);

		$slot = BookingSlots::create([
			'booking_id' => $booking->id,
			'start_time' => '2025-08-03 14:00:00',
			'end_time'   => '2025-08-03 15:00:00',
		]);

		$response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
			->postJson("/api/bookings/{$booking->id}/slots",[
				'slot' => [
					'start_time' => '2025-08-03 14:30:00',
					'end_time'   => '2025-08-03 15:30:00'
				]
			]);
		
		$response->assertStatus(422);
		$response->assertJsonPath('message', 'Время занято другим бронированием');
	}

	/**
	 * Обновление слота - успешно;
	 */
	public function testSuccessedUpdateSlot()
	{
		$booking = Bookings::create(['user_id' => $this->user->id]);

		$slot = BookingSlots::create([
			'booking_id' => $booking->id,
			'start_time' => '2025-08-03 10:00:00',
			'end_time'   => '2025-08-03 11:00:00',
		]);

		$response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
			->patchJson("/api/bookings/{$booking->id}/slots/{$slot->id}", [
				'slot' => [
					'start_time' => '2025-08-03 13:00:00',
					'end_time'   => '2025-08-03 14:00:00',
				],
			]);
		
		$response->assertStatus(200);
		$this->assertDatabaseHas('booking_slots', [
			'id'         => $slot->id,
			'start_time' => '2025-08-03 13:00:00',
			'end_time'   => '2025-08-03 14:00:00',
		]);
	}

	/**
	 *Обновление слота - неуспешно
	 */
	public function testUnsuccessedUpdateSlot()
	{
		$booking = Bookings::create(['user_id' => $this->user->id]);

		$slot = BookingSlots::create([
			'booking_id' => $booking->id,
			'start_time' => '2025-08-03 10:00:00',
			'end_time'   => '2025-08-03 11:00:00',
		]);

		$anotherSlot = BookingSlots::create([
			'booking_id' => $booking->id,
			'start_time' => '2025-08-03 12:00:00',
			'end_time'   => '2025-08-03 13:00:00',
		]);

		$response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
			->patchJson("/api/bookings/{$booking->id}/slots/{$slot->id}", [
				'slot' => [
					'start_time' => '2025-08-03 10:30:00',
					'end_time'   => '2025-08-03 12:30:00',
				],
			]);
		
		$response->assertStatus(422);
		$response->assertJsonPath('message', 'Время занято другим бронированием');
	}

	/**
	 * Авторизация - запрос без токена отклоняется
	 */
	public function testGetBookingsWithoutToken()
	{
		$response = $this->getJson('/api/bookings');

		$response->assertStatus(401);
		$response->assertJsonPath('message', 'Требуется API-токен');
	}
}
