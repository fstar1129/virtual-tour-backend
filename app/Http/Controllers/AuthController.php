<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Http\Requests\SignupRequest;
use App\Http\Resources\UserSessionResource;
use Laravel\Socialite\Facades\Socialite;
use App\Events\UserWasRegistered;
use App\Events\MobileUserWasRegistered;
use Carbon\Carbon;
use Config;

class AuthController extends Controller
{
    /**
     * Validate user credentials and return JWT token
     *
     * @param LoginRequest $req
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $req)
    {
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($req->validated())) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // don't allow login if user is not active.
        if (! auth()->user()->active) {
            return $this->fail(401, 'Your account has been disabled.');
        }
        // all good so return the token
        return response()->json([
            'user' => new UserSessionResource(auth()->user()),
            'token' => $token,
        ]);
    }

    /**
     * Register user and return JWT token
     *
     * @param SignupRequest $req
     * @return \Illuminate\Http\Response
     */
    public function signup(SignupRequest $req)
    {
        $data = $req->validated();

        $attributes = [
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => bcrypt($data['password']),
            'latitude' => isset($data['latitude']) ? $data['latitude'] : null,
            'longitude' => isset($data['longitude']) ? $data['longitude'] : null,
            'active' => 1,
            'user_type' => $req->role == 'client' ? 1 : 2
        ];

        switch ($req->role) {
            case 'client':
                $attributes['tour_limit'] = 1;
                $user = \App\Client::create($attributes);
                event(new UserWasRegistered($user));
                break;
            case 'user':
            default:
                $attributes['tour_limit'] = 1;
                $user = \App\MobileUser::create($attributes);
                event(new MobileUserWasRegistered($user));
                break;
        }

        /* ------- Add a contact to sendinblue ----------------- */
        $curl = curl_init();

        curl_setopt_array( $curl, array(
            CURLOPT_URL            => "https://api.sendinblue.com/v3/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => json_encode( [
                    'email'            => strtolower($data['email']),
                    'emailBlacklisted' => false,
                    'updateEnabled'    => true,
                    'listIds'          => [ 5 ], // Campaign List ID's
                    'attributes'       => [ 'FIRSTNAME' => $data['name'], 'LASTNAME' => ''],
                ] ),
            CURLOPT_HTTPHEADER     => array(
                "accept: application/json",
                sprintf( "api-key: %s", config('mail.sendinblue_api_key') ),
                "content-type: application/json"
            ),
        ) );

        $response = curl_exec( $curl );
        $err      = curl_error( $curl );
        
        curl_close( $curl );

        /* ------- Add a contact to sendinblue ----------------- */
        return $this->createTokenForUser($user);
    }

    /**
     * Creates a JWT for the given User model and returns the user/token response.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function createTokenForUser($user)
    {
        try {
            if (! $token = JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json([
            'user' => new UserSessionResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Get the current user session
     *
     * @return \Illuminate\Http\Response
     */
    public function userSession()
    {
        return response()->json(new UserSessionResource(auth()->user()));
    }

    /**
     * Detach FB account information from authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function facebookDetach()
    {
        auth()->user()->update([
            'fb_id' => null,
            'fb_token' => null,
        ]);

        return response()->json(['success' => 1]);
    }

    /**
     * Handles user authentication using Facebook access token.
     *
     * @return \Illuminate\Http\Response
     */
    public function facebookAttach()
    {
        $user = User::findByFacebookId(request()->fb_id);

        if ($user && $user->id != auth()->id()) {
            // facebook already attached to another account
            return response()->json(['error' => 'fb_exists'], 401);
        }

        auth()->user()->update([
            'fb_id' => request()->fb_id,
            'fb_token' => request()->fb_token,
        ]);

        return response()->json(['success' => 1]);
    }

    /**
     * Handles user authentication using Facebook access token.
     *
     * @return \Illuminate\Http\Response
     */
    public function facebook()
    {
        if (empty(request()->fb_id) || empty(request()->fb_token)) {
            return $this->fail(401, 'Facebook token is invalid!');
        }

        $user = User::where('email', request()->email)
                    ->where('fb_id', request()->fb_id)
                    ->first();

        // don't allow login if user is not active.
        if (! empty($user) && ! $user->active) {
            return $this->fail(401, 'Your account has been disabled.');
        }

        // if still no user, create one
        if (empty($user)) {
            $attributes = [
                'name' => request()->name,
                'email' => request()->email,
                'fb_id' => request()->fb_id,
                'fb_token' => request()->fb_token,
                'password' => bcrypt(request()->fb_token),
                'user_type' => request()->role == 'client' ? 1 : 2,
                'email_confirmed_at' => Carbon::now()
            ];

            switch (request()->role) {
                case 'client':
                    $user = \App\Client::create($attributes);
                    break;
                case 'user':
                default:
                    $user = \App\MobileUser::create($attributes);
                    break;
            }
        } else {
            $user->update([
                'fb_id' => request()->fb_id,
                'fb_token' => request()->fb_token,
            ]);
        }

        return $this->createTokenForUser($user);
    }

    /**
     * Handles user authentication using Google access token.
     *
     * @return \Illuminate\Http\Response
    */
    public function google() {

        if (empty(request()->google_id) || empty(request()->google_token)) {
            return $this->fail(401, 'Google token is invalid!');
        }

        $user = User::where('email', request()->email)
                    ->where('google_id', request()->google_id)
                    ->first();
        // don't allow login if user is not active.
        if (! empty($user) && ! $user->active) {
            return $this->fail(401, 'Your account has been disabled.');
        }

        if (empty($user)) {
            $attributes = [
                'name' => request()->name,
                'email' => request()->email,
                'google_id' => request()->google_id,
                'google_token' => request()->google_token,
                'password' => bcrypt(request()->google_token),
                'user_type' => request()->role == 'client' ? 1 : 2,
                'email_confirmed_at' => Carbon::now()
            ];

            switch (request()->role) {
                case 'client':
                    $user = \App\Client::create($attributes);
                    break;
                case 'user':
                default:
                    $user = \App\MobileUser::create($attributes);
                    break;
            }
        } else {
            $user->update([
                'google_id' => request()->google_id,
                'google_token' => request()->google_token,
            ]);
        }

        return $this->createTokenForUser($user);
    }
}
