<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialiteController extends Controller
{
    protected function getDriver(string $provider): string
    {
        return $provider === 'linkedin' ? 'linkedin-openid' : $provider;
    }

    public function redirect(string $provider)
    {
        return Socialite::driver($this->getDriver($provider))->redirect();
    }

    public function callback(string $provider)
    {
        $driver = $this->getDriver($provider);

        try {
            $socialUser = Socialite::driver($driver)->user();
        } catch (Exception $e) {
            Log::error("{$provider} Login Error: " . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'error' => 'Unable to authenticate with ' . ucfirst($provider) . '. Please try again.'
            ]);
        }

        $user = User::where('provider_id', $socialUser->getId())
                    ->orWhere('email', $socialUser->getEmail())
                    ->first();

        if ($user) {
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Customer',
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'password' => null,
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended('/shop');
    }
}