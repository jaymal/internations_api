<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{

    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
       
        $http = new Client;

        $response = $http->post(url('oauth/token'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '2',
                'client_secret' => 'jpnfvu48cXCrAi6JXxWJU6UK4gb51gLG0duPHA6H',
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '',
            ],
        ]);


        return response(['auth'=>json_decode((string) $response->getBody(), true),'user'=>$user]);
 
    }
 
    /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
       
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);

        $user= User::where('email',$request->email)->first();

        if(!$user){
            return response(['status'=>'error','message'=>'User not found']);
        }

        if(Hash::check($request->password, $user->password)){

            $http = new Client;

            $response = $http->post(url('oauth/token'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => '2',
                    'client_secret' => 'jpnfvu48cXCrAi6JXxWJU6UK4gb51gLG0duPHA6H',
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' => '',
                ],
            ]);
            return response(['auth' => json_decode((string)$response->getBody(), true), 'user' => $user]);

        
        }else{
            return response(['message'=>'password not match','status'=>'error']);
        }
    }
    
    /**
     *  Handles exchanging a refresh token for an access token when the access token has expired
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken() 
    {

        $http = new Client;

        $response = $http->post(url('oauth/token'), [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => request('refresh_token'),
                'client_id' => '2',
                'client_secret' => 'A3ows1vtGRWoQ2Arz296N1tgGyllFUGkJahhYWKU',
                'scope' => '',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);

    }
}