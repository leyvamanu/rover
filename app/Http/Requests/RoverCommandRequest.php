<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoverCommandRequest extends FormRequest
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
            'position' => ['required', 'array'],
            'position.x' => ['required', 'integer'],
            'position.y' => ['required', 'integer'],
            'direction' => ['required', 'string', 'in:N,S,E,W'],
            'commands' => ['required', 'string', 'regex:/^[FRL]+$/'],
            'obstacles' => ['array'],
            'obstacles.*' => ['array'],
            'obstacles.*.x' => ['required_with:obstacles.*', 'integer'],
            'obstacles.*.y' => ['required_with:obstacles.*', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'position.required' => 'La posición es requerida',
            'position.array' => 'La posición debe ser un array',
            'position.x.required' => 'La coordenada X es requerida',
            'position.x.integer' => 'La coordenada X debe ser un número entero',
            'position.y.required' => 'La coordenada Y es requerida',
            'position.y.integer' => 'La coordenada Y debe ser un número entero',
            'direction.required' => 'La dirección es requerida',
            'direction.string' => 'La dirección debe ser una cadena de texto',
            'direction.in' => 'La dirección debe ser N, S, E o W',
            'commands.required' => 'Los comandos son requeridos',
            'commands.string' => 'Los comandos deben ser una cadena de texto',
            'commands.regex' => 'Los comandos solo pueden contener F, R o L',
            'obstacles.array' => 'Los obstáculos deben ser un array',
            'obstacles.*.array' => 'Cada obstáculo debe ser un array',
            'obstacles.*.x.required_with' => 'La coordenada X del obstáculo es requerida',
            'obstacles.*.x.integer' => 'La coordenada X del obstáculo debe ser un número entero',
            'obstacles.*.y.required_with' => 'La coordenada Y del obstáculo es requerida',
            'obstacles.*.y.integer' => 'La coordenada Y del obstáculo debe ser un número entero',
        ];
    }
}
