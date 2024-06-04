<?php
// app/Http/Requests/UpdateCategoriaRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoriaRequest extends FormRequest
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
        $categoriaId = $this->route('categoria'); // Obtiene el ID de la categoría actual de la ruta
        return [
            'nombre' => 'required|max:60|unique:categorias,nombre,' . $categoriaId,
            'descripcion' => 'nullable|max:255',
        ];
    }
}
