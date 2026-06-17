<?php

namespace App\Http\Requests;

use App\Enums\MassMessageChannelEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MassMessageIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'channel' => ['required', Rule::enum(MassMessageChannelEnum::class)],
            'limit' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'channel.required' => 'Канал связи обязателен для заполнения.',
            'channel.in' => 'Допустимые каналы: sms или email.',
            'limit.required' => 'Количество записей обязано быть указано.',
            'limit.integer' => 'Параметр limit должен быть целым числом.',
            'limit.min' => 'Минимальное значение для limit: 1.',
            'limit.max' => 'Максимальное значение для limit: 50.',
        ];
    }
    public function attributes(): array
    {
        return [
            'channel' => 'канал связи',
            'limit' => 'количество записей',
        ];
    }
}
