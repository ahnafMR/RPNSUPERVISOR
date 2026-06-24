<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LokasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('lokasi')?->id;

        return [
            'kode_lokasi' => 'required|string|max:50|unique:lokasi,kode_lokasi,' . $id,
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:10|max:5000',
            'status' => 'required|in:aktif,tidak_aktif',
        ];
    }
}
