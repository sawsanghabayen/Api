<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthController extends Controller
{

  
    public function login(Request $request)
    {
        $validator = Validator($request->all(), [
            'email' => 'required_if:mobile,==,null|email|exists:users,email',
            'mobile' => 'required_if:email,==,null|numeric:users,mobile|digits:9',
            'password' => 'required|string|min:3',
        ]);

        if (!$validator->fails()) {
            $user = User::where('email', '=', $request->input('email'))->orWhere('mobile' ,$request->input('mobile'))->first();

            if ($user->verified && $user->active) {

                if (Hash::check($request->input('password'), $user->password)) {
                    $token = $user->createToken('User-API');
                    $user->setAttribute('token', $token->accessToken);
                    return response()->json([
                        'status' => true,
                        'message' => 'Logged in successfully',
                        'object' => $user,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Wrong credentials, check password!',
                    ], Response::HTTP_BAD_REQUEST);
                }
            } else {
                $message = '';
                if (!$user->active) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Account is blocked',
                    ], Response::HTTP_BAD_REQUEST);
                } else if (!$user->verified) {

                    
                    return response()->json([
                        'status' => false,
                        'message' => 'Account must be activated to enable login!',
                    ], Response::HTTP_BAD_REQUEST);
                }
                return response()->json([
                    'status' => false,
                    'message' => $message,
                ], Response::HTTP_BAD_REQUEST);
            }
      
           
        } else {
            return response()->json(
                ['message' => $validator->getMessageBag()->first()],
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator($request->all(), [
            'password' => 'required|string|current_password:user-api',
            'new_password' => 'required|string|min:3|confirmed',
        ]);

        if (!$validator->fails()) {
            // $usre = auth('user-api')->user();
            $user = $request->user();
            $user->password = Hash::make($request->input('new_password'));
            $isSaved = $user->save();
            return response()->json([
                'status' => $isSaved,
                'message' => $isSaved ? 'Password updated successfully' : 'Password update failed!',
            ], $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function forgetPassword(Request $request)
    {
        $validator = Validator($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if (!$validator->fails()) {
            $user = User::where('email', '=', $request->input('email'))->first();

            $randomCode = random_int(1000, 9999);
            $user->verification_code = Hash::make($randomCode);
            $isSaved = $user->save();
            return response()->json([
                'status' => $isSaved,
                'message' => $isSaved ? 'Code sent successfully' : 'Code sending failed!',
                'code' => $randomCode,
            ], $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric|digits:4',
            'new_password' => 'required|string|min:3|confirmed'
        ]);

        if (!$validator->fails()) {
            $user = User::where('email', '=', $request->input('email'))->first();
            if (!is_null($user->verification_code)) {
                if (Hash::check($request->input('code'), $user->verification_code)) {
                    $user->password = Hash::make($request->input('new_password'));
                    $user->verification_code = null;
                    $isSaved = $user->save();
                    return response()->json([
                        'status' => $isSaved,
                        'message' => $isSaved ? 'Password reset successfully' : 'Password reset failed!',
                    ], $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Reset process rejected, no verification code!',
                ], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateProfile(Request $request ,User $user)
    {

        $roles = [
            'name' => 'required|string|min:3',
            'email' => 'nullable|string|email|unique:users,email,' .$request->user()->id,
            'mobile' => 'required|numeric|digits:9|unique:users,mobile,' .$request->user()->id,
            'birthdate' => 'required|date',
            'gender' => 'required|string|in:M,F',

        ];
        $validator = Validator($request->all(), $roles);
        if (!$validator->fails()) {
            
            $user = $request->user('user-api');
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->mobile = $request->input('mobile');
            $user->birthdate = $request->input('birthdate');
            $user->gender = $request->input('gender');
            $isSaved = $user->save();
            return response()->json([
                'status' => $isSaved,
                'message' => $isSaved ? 'Profile Updated successfully' : 'Profile Updating failed!',
            ], $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
    public function register(Request $request)
    {
        $roles = [
            'name' => 'required|string|min:3',
            'email' => 'required|email',
            'mobile' => 'required|numeric|unique:users,mobile|digits:9',
            'password' => 'required|String|min:3',
            
        ];
        $validator = Validator($request->all(), $roles);
        if (!$validator->fails()) {
            $user = new User();
            $user->name = $request->input('name');
            $user->mobile = $request->input('mobile');
            $user->password = Hash::make($request->input('password'));
            $user->email = $request->input('email');
            $isSaved = $user->save();
            if ($isSaved) {
            return $this->sendActivationCode($user);
            }

            // return response()->json([
            //     'status' => $isSaved,
            //     'message' => $isSaved ? 'Registering successfully '  : 'Register failed!',
            // ], $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
        }
         else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function sendActivationCode($user)
    {
        $code = random_int(1000, 9999);
        $user->verification_code = Hash::make($code);
        $user->save();
        return response()->json([
            'status' => true,
            'message' => 'Auth code sent successfully',
            'code' => $code
        ], Response::HTTP_CREATED);
    }

    public function activateAccount(Request $request)
    {
        $validator = Validator($request->all(), [
            'mobile' => 'required|numeric|digits:9|exists:users,mobile',
            'code' => 'required|numeric|digits:4',
        ]);

        if (!$validator->fails()) {
            $user =  User::where('mobile', '=', $request->input('mobile'))->first();
            // dd($request->input('mobile'));

            if (!is_null($user->verification_code)) {
                if (Hash::check($request->get('code'), $user->verification_code)) {
                    $user->verification_code = null;
                    $user->verified = true;
                    $isSaved = $user->save();
                    //if ($isSaved) $user->assignRole(Role::findByName('Customer-API', 'user-api'));
                    return response()->json([
                        'status' => $isSaved,
                        'message' => $isSaved ? 'Account activated successfully' : 'Account activation failed, try again',
                    ], $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Activation code error, try again',
                    ], Response::HTTP_BAD_REQUEST);
                }
            } else {
                if ($user->verified) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Account has been verified before, rejected action!',
                    ], Response::HTTP_BAD_REQUEST);
                } else {
                    return $this->sendActivationCode($user);
                }
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }


    public function logout(Request $request)
    {
        $revoked = $request->user('user-api')->token()->revoke();
        return response()->json(
            ['message' => $revoked ? 'Logged out successfully' : 'Logout failed!'],
            $revoked ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        );
    }


}
