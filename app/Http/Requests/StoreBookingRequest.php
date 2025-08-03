<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slots'              => 'required|array|min:1',
            'slots.*.start_time' => 'required|date',
            'slots.*.end_time'   => 'required|date|after:slots.*.start_time',
        ];
    }

    public function message()
    {
        return [
            'slots.requried'              => 'Укажите хотя бы один временной слот',
            'slots.array'                 => 'Поле slot должно быть массивом',
            'slots.min'                   => 'Должен быть указан минимум один слот',
            'slots.*.start_time.required' => 'Должно быть указано время начала слота',
            'slots.*.start_time.date'     => 'Поле должно быть корректной даты',
            'slots.*.end_time.required'   => 'Должно быть указано время окончания слота',
            'slots.*.end_time.date'       => 'Поле должно быть корректной даты',
            'slots.*.end_time.after'      => 'Время окончания слота должно быть позже времени начала',
        ];
    }
}
