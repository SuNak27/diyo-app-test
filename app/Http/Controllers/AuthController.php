<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwtmiddleware', ['except' => ['register', 'login']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:3,100',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $data = $validator->validated();

            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            return response()->json([
                'code' => Response::HTTP_CREATED,
                'message' => 'Success',
                'data' => $user
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage(),
                'data' => $e->getTrace()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if (!$token = auth()->attempt($validator->validated())) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $customClaim = [
                'user' => auth()->user()
            ];

            $token = JWTAuth::claims($customClaim)->attempt($validator->validated());

            return response()->json([
                'code' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => auth()->user()
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage(),
                'data' => $e->getTrace()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout()
    {
        try {
            auth()->logout();

            return response()->json([
                'code' => Response::HTTP_OK,
                'message' => 'Successfully logged out',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage(),
                'data' => $e->getTrace()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
