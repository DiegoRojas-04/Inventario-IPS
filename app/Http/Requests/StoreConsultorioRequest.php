<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultorioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permitir la autorización para este request
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|unique:consultorios,nombre|max:60', // Nombre obligatorio y único
            'descripcion' => 'nullable|max:255', // Descripción opcional con un máximo de 255 caracteres
        ];
    }
}
