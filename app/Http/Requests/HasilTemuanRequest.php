<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HasilTemuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_selesai' => 'required|date',
            'hasil_perbaikan' => 'required|string',
            'catatan_akhir' => 'nullable|string',
            'foto_hasil' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'status' => 'required|in:selesai,belum_selesai',
        ];
    }
}
