<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInsumoRequest extends FormRequest
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
        return [
            'nombre' => 'required | max:60 | unique:categorias,nombre',
            'descripcion' => 'nullable | max:255',
            'stock' => 'required | numeric | min:0',
            'riesgo' => 'required',
            'vida_util' => 'required',
            'id_categoria' => 'required',
            'id_marca' => 'required',
            'id_presentacion' => 'required',
        ];
    }
}
