<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul_temuan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tingkat_risiko' => 'required|in:rendah,sedang,tinggi',
            'rekomendasi' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'foto' => 'nullable|array',
            'foto.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ];
    }
}
