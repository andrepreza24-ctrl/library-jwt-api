<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Models\Book;

class BookController extends Controller
{
    /**
     * GET /api/books
     * Protegido por el middleware auth:api (ver routes/api.php).
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Book::orderBy('created_at', 'desc')->paginate(10),
        ]);
    }

    /**
     * GET /api/books/{id}
     */
    public function show(Book $book)
    {
        return response()->json([
            'success' => true,
            'data' => $book,
        ]);
    }

    /**
     * POST /api/books
     */
    public function store(BookRequest $request)
    {
        $book = Book::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Libro creado correctamente.',
            'data' => $book,
        ], 201);
    }

    /**
     * PUT/PATCH /api/books/{id}
     */
    public function update(BookRequest $request, Book $book)
    {
        $book->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Libro actualizado correctamente.',
            'data' => $book,
        ]);
    }

    /**
     * DELETE /api/books/{id}
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Libro eliminado del catálogo.',
        ]);
    }
}
