<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUploadRequest extends FormRequest
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
            //
        ];
    }

    public function update(UpdateUploadRequest $request, $id)
    {
        $upload = Upload::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        // Validasi khusus untuk PK
        if ($request->jenis_putusan == 'pk' && empty($request->nomor_perkara_pk)) {
            return back()->withErrors([
                'nomor_perkara_pk' => 'Nomor perkara PK wajib diisi untuk putusan PK'
            ]);
        }

        // ... sisanya sama ...
    }
}
