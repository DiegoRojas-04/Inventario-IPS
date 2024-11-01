<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivoRequest extends FormRequest
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
        $activoId = $this->route('activo') ? $this->route('activo') : null;
        return [
            'codigo' => 'nullable|string|max:255|unique:activos,codigo,' . $activoId,
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categoria_activos,id',
            'modelo' => 'nullable|string|max:255',
            'serie' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'cantidad' => 'required|integer|min:1',
            'medida' => 'nullable|string|max:50',
            'estado' => 'required|string|max:50',
            'ubicacion_id' => 'required',
            'observacion' => 'nullable|string|max:500',
        ];
    }
}
