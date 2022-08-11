<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\JWTAuth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends Controller
{
    public $successStatus = 200;
    public $createdStatus = 201;
    public $acceptedStatus = 202;
    public $badRequestStatus = 400;
    public $unauthorized = 401;
    public $notFound = 404;

     /* *** START FOR ADMIN SERVICE *** */
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function userLogin(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:4'
        ]);

        $credentials = $request->only(['email', 'password']);


        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['success' => false, 'error' => 'Unauthorized', 'code' => '401'], $this->unauthorized);
        }

        // return $this->respondWithToken($token);
        // try{
        //     if(! $token = auth()->attempt($credentials)){
        //         throw new UnauthorizedHttpException('Involid Credentials');
        //     }
        // } catch(JWTException $e){
        //     return response()->json(['error' => 'Could not create Token!']);
        // }

        // return response()->json(compact('token'));
        return $this->respondWithToken($token);
    }


     /* *** START FOR ADMIN SERVICE *** */
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function userRegister(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], $this->badRequestStatus);
        }

        $req = $request->all();
        $req['password'] =  Hash::make($req['password']);
        $user = \App\Models\User::create($req);

        if($user){
            
            DB::table('model_has_roles')->insert([
                'role_id' => $req['role_id'],
                'model_type' => 'App\Models\User',
                'model_id' => $user->id
            ]);

            return response()->json(['success' => true, 'code' => '201'], $this->createdStatus);
        }else {
            return ['dsdsd'];
        }


    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(): \Illuminate\Http\JsonResponse
    {
        return response()->json(Auth::user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): \Illuminate\Http\JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL(),
            'user' => \auth()->user()
        ]);
    }
}
