<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Utils\ResponseUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 *    @OA\Post(
 *       path="/login",
 *       tags={"Auth"},
 *       operationId="loginUser",
 *       summary="User Login",
 *       description="User login with email and password",
 *       @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *          )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Login successful",
 *           @OA\JsonContent(
 *               @OA\Property(property="success", type="boolean", example=true),
 *               @OA\Property(property="message", type="string", example="Login successful"),
 *               @OA\Property(
 *                  property="data",
 *                  type="object",
 *                  @OA\Property(property="token", type="string", example="jwt_token_here"),
 *                  @OA\Property(property="user", type="object",
 *                      @OA\Property(property="id", type="integer", example=1),
 *                      @OA\Property(property="name", type="string", example="John Doe"),
 *                      @OA\Property(property="email", type="string", example="user@example.com")
 *                  )
 *               )
 *           )
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Unauthorized",
 *           @OA\JsonContent(
 *               @OA\Property(property="success", type="boolean", example=false),
 *               @OA\Property(property="message", type="string", example="Invalid credentials")
 *           )
 *       )
 *    )
 */

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ResponseUtils::error('Invalid email or password', 401);
        }

        // Create token for the authenticated user
        $token = $user->createToken('auth_token')->plainTextToken;


        $data = [
            'token' => $token,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first(), // Assuming you're using a role system like Spatie Roles
        ];
        return ResponseUtils::success($data, 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ResponseUtils::success(null, 'Logout successful');
    }

}
