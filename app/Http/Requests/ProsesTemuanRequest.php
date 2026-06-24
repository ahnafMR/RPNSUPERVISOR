<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProsesTemuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_proses' => 'required|date',
            'pic' => 'required|string|max:255',
            'tindakan' => 'required|string',
            'catatan' => 'nullable|string',
            'foto_proses' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ];
    }
}
