<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUbicacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|unique:ubicaiones,nombre|max:60', // Nombre obligatorio y único
            'descripcion' => 'nullable|max:255', // Descripción opcional con un máximo de 255 caracteres
        ];
    }
}
