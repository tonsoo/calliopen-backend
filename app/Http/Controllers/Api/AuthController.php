<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Authentication\InvalidLoginException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Json\BasicClientJson;
use App\Http\Resources\Json\ClientJson;
use App\Models\Client;
use App\Services\AuthenticationService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    public function login(Request $request) : JsonResponse {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $login = $data['login'];
        $password = $data['password'];

        try {
            $service = app(AuthenticationService::class);
            $client = $service->authenticate($login, $password);
            $token = $client->createToken('api')->plainTextToken;
            return Response::json([
                'token' => $token,
                'type' => 'bearer'
            ]);
        } catch (InvalidLoginException $e) {
            return Response::json(['error' => 'Invalid credentials'], 401);
        } catch (ModelNotFoundException $e) {
            return Response::json(['error' => 'Client doesn\'t exist'], 404);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return Response::json(['error' => 'Unknown error'], 500);
        }
    }

    public function register(Request $request) : JsonResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:1'],
            'username' => ['required', 'string', 'max:255', 'min:1', 'unique:'.Client::class],
            'password' => ['required', 'string', 'max:255', 'min:6'],
            'email' => ['required', 'email', 'max:255', 'min:6', 'unique:'.Client::class],
        ]);

        try {
            $client = new Client($data);
            $client->save();
        } catch (UniqueConstraintViolationException $e) {
            return Response::json(['error' => 'Client with given data already exists'], 409);
        }
        
        return Response::json(new ClientJson($client));
    }

    public function information(?Client $client, Request $request) : JsonResponse {
        if ($client?->id) {
            return Response::json(new BasicClientJson($client));
        }

        return Response::json(new ClientJson($request->user()));
    }
}
