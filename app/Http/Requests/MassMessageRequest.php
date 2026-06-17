<?php

namespace App\Http\Requests;

use App\Enums\MassMessageChannelEnum;
use App\Enums\PriorityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MassMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'channel' => ['required', Rule::enum(MassMessageChannelEnum::class)],
            'priority' => ['required', Rule::enum(PriorityEnum::class)],
            'message' => ['required', 'string', 'max:10000'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'channel.required' => 'Канал связи обязателен для заполнения.',
            'channel.in' => 'Допустимые каналы: sms или email.',
            'priority.required' => 'Приоритет доставки обязателен для заполнения.',
            'priority.in' => 'Допустимые приоритеты: low, normal, high.',
            'message.required' => 'Текст сообщения обязателен для заполнения.',
            'message.max' => 'Максимальная длина сообщения: 10000 символов.',
            'user_ids.required' => 'Массив идентификаторов получателей обязателен.',
            'user_ids.array' => 'Поле user_ids должно быть массивом.',
            'user_ids.min' => 'Должен быть указан хотя бы один получатель.',
            'user_ids.*.exists' => 'Пользователь с ID :value не найден.',
        ];
    }
    public function attributes(): array
    {
        return [
            'channel' => 'канал связи',
            'message' => 'текст сообщения',
            'user_ids' => 'идентификаторы получателей',
        ];
    }
}
