<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AdminAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i'],

            'breaks.*.break_start' => ['nullable', 'date_format:H:i'],
            'breaks.*.break_end' => ['nullable', 'date_format:H:i'],

            'note' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {

            $clockIn = $this->input('clock_in');
            $clockOut = $this->input('clock_out');

            if ($clockIn && $clockOut && $clockIn >= $clockOut) {
                $validator->errors()->add(
                    'clock_in',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            if (!$clockIn || !$clockOut) {
                return;
            }

            foreach ($this->input('breaks', []) as $index => $break) {

                $breakStart = $break['break_start'] ?? null;
                $breakEnd = $break['break_end'] ?? null;

                if ($breakStart && ($breakStart < $clockIn || $breakStart > $clockOut)) {
                    $validator->errors()->add(
                        "breaks.$index.break_start",
                        '休憩時間が不適切な値です'
                    );
                }

                if ($breakEnd && $breakEnd > $clockOut) {
                        $validator->errors()->add(
                            "breaks.$index.break_end",
                            '休憩時間もしくは退勤時間が不適切な値です'
                        );
                }

                if ($breakStart && $breakEnd && $breakStart >= $breakEnd) {
                    $validator->errors()->add(
                        "breaks.$index.break_start",
                        '休憩時間が不適切な値です'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'clock_in.required' => '出勤時間を入力してください',
            'clock_in.date_format' => '出勤時間は「時:分」の形式で入力してください',

            'clock_out.required' => '退勤時間を入力してください',
            'clock_out.date_format' => '退勤時間は「時:分」の形式で入力してください',

            'breaks.*.break_start.date_format' => '休憩開始時間は「時:分」の形式で入力してください',
            'breaks.*.break_end.date_format' => '休憩終了時間は「時:分」の形式で入力してください',

            'note.required' => '備考を記入してください',
            'note.max' => '備考は255文字以内で入力してください',
        ];
    }
}
