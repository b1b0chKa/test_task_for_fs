<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Fasades\Auth;

class AuthenticateApiToken
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		$token = $request->bearerToken();

		if (!$token)
		{
			return response()->json([
				'message' => 'Требуется API-токен'
			], 401);
		}

		$user = App\Models\User::where('api_token', $token)->first();

		if(!$user)
		{
			return response()->json([
				'message' => 'Неверный токен',
			], 401);
		}

		Auth::setUser($user);
		$request->setUser('user', $user);

		return $next($request);
	}
}
