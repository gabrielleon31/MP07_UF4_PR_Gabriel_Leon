<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Registrar un nou usuari",
     *     tags={"Autenticació"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="Usuari Nou"),
     *             @OA\Property(property="email", type="string", format="email", example="usuari@email.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Usuari registrat correctament"),
     *     @OA\Response(response=422, description="Dades no vàlides")
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role, 
        ]);

        return response()->json(['message' => 'Usuari registrat correctament'], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login d'usuari",
     *     tags={"Autenticació"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="usuari@email.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login correcte i retorn del token"),
     *     @OA\Response(response=422, description="Credencials incorrectes")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
                'errors' => [
                    'email' => ['Las credenciales proporcionadas no son correctas.']
                ]
            ], 422);
        }
    
        $token = $user->createToken('api-token')->plainTextToken;
    
        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role // Asegúrate que este campo existe
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Tancar sessió",
     *     tags={"Autenticació"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Sessió tancada correctament"),
     *     @OA\Response(response=401, description="No autenticat")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sessió tancada correctament']);
    }
}
