<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Llistar productes",
     *     tags={"Productes"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Llista de productes"
     *     )
     * )
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Crear un producte",
     *     tags={"Productes"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price"},
     *             @OA\Property(property="name", type="string", example="Producte 1"),
     *             @OA\Property(property="description", type="string", example="Descripció opcional"),
     *             @OA\Property(property="price", type="number", format="float", example=19.99)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Producte creat correctament"),
     *     @OA\Response(response=403, description="No tens permisos")
     * )
     */
    public function store(StoreProductRequest $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'No tens permisos'], 403);
        }

        $product = Product::create($request->validated());

        return response()->json($product, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Actualitzar un producte",
     *     tags={"Productes"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producte",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price"},
     *             @OA\Property(property="name", type="string", example="Producte actualitzat"),
     *             @OA\Property(property="description", type="string", example="Nova descripció"),
     *             @OA\Property(property="price", type="number", format="float", example=25.50)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Producte actualitzat"),
     *     @OA\Response(response=403, description="No tens permisos"),
     *     @OA\Response(response=404, description="Producte no trobat")
     * )
     */
    public function update(UpdateProductRequest $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'No tens permisos'], 403);
        }

        $product = Product::findOrFail($id);
        $product->update($request->validated());

        return response()->json($product);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Eliminar un producte",
     *     tags={"Productes"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producte",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Producte eliminat"),
     *     @OA\Response(response=403, description="No tens permisos"),
     *     @OA\Response(response=404, description="Producte no trobat")
     * )
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'No tens permisos'], 403);
        }

        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Producte eliminat']);
    }
}
