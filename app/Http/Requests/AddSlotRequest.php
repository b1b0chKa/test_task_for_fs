<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddSlotRequest extends FormRequest
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
            'slot'            => 'required|array',
            'slot.start_time' => 'required|date',
            'slot.end_time'   => 'required|date|after:slot.start_time',
        ];
    }

    public function message()
    {
        return [
            'slot.requried'              => 'Укажите хотя бы один временной слот',
            'slot.array'                 => 'Поле slot должно быть массивом',
            'slot.*.start_time.required' => 'Должно быть указано время начала слота',
            'slot.*.start_time.date'     => 'Поле должно быть корректной даты',
            'slot.*.end_time.required'   => 'Должно быть указано время окончания слота',
            'slot.*.end_time.date'       => 'Поле должно быть корректной даты',
            'slot.*.end_time.after'      => 'Время окончания слота должно быть позже времени начала',
        ];
    }
}
