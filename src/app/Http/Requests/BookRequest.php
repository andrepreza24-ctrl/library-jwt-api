<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $bookId = $this->route('book')?->id;
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => [
                'required',
                'string',
                'max:20',
                Rule::unique('books', 'isbn')->ignore($bookId),
            ],
            'available_copies' => ['required', 'integer', 'min:0'],
        ];
    }
    public function messages(): array
    {
        return [
            'isbn.unique' => 'Ya existe un libro registrado con ese ISBN.',
        ];
    }
}