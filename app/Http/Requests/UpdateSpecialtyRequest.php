<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpecialtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $specialty = $this->route('specialty');
        $specialtyId = is_object($specialty) ? $specialty->id : $specialty;

        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('specialties', 'name')->ignore($specialtyId)],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('specialties', 'slug')->ignore($specialtyId)],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên chuyên khoa không được để trống.',
            'name.max' => 'Tên chuyên khoa tối đa 150 ký tự.',
            'name.unique' => 'Tên chuyên khoa này đã tồn tại.',
            'slug.unique' => 'Đường dẫn tĩnh (slug) này đã được sử dụng.',
        ];
    }
}
