<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            'jenis_putusan' => ['required', Rule::in(['kasasi', 'pk'])],
            'tanggal_putusan' => 'required|date',
            'nomor_perkara_pa' => 'required|string|max:100',
            'nomor_perkara_banding' => 'nullable|string|max:100',
            'file' => 'required|file|mimes:pdf|max:10240',
        ];

        if ($this->jenis_putusan === 'kasasi') {
            $rules['nomor_perkara_kasasi'] = 'required|string|max:100';
            $rules['nomor_perkara_pk'] = 'nullable|string|max:100';
        } else {
            $rules['nomor_perkara_kasasi'] = 'required|string|max:100';
            $rules['nomor_perkara_pk'] = 'required|string|max:100';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nomor_perkara_pa.required' => 'Nomor perkara PA wajib diisi',
            'nomor_perkara_kasasi.required' => 'Nomor perkara kasasi wajib diisi',
            'nomor_perkara_pk.required' => 'Nomor perkara PK wajib diisi untuk putusan PK',
            'file.mimes' => 'File harus dalam format PDF',
            'file.max' => 'Ukuran file maksimal 10MB',
        ];
    }
}
