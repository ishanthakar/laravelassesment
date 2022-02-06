<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;

class LoginController extends BaseController
{
    use CommonTrait;

    public function __construct()
    {
        //
    }

    /**
     * Login User.
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response mixed
     **/
    protected function authenticate(Request $request)
    {
        try {
            $postData = $request->all();

            $validator = \Validator::make($postData, [
                'username' => "required",
                'password' => "required|min:8|max:20",
            ], [
                'username.required' => "Please enter email/username!",
                'password.required' => "Please enter password!",
                'password.min'      => "Please enter valid password with min 8 characters!",
                'password.max'      => "Please enter valid password with max 20 characters!",
            ]);

            if ($validator->fails()) {
                return $this->sendError('Failed to login!', $validator->errors(), 400);
            }

            [$login, $password] = [$postData['username'], $postData['password']];
            $token              = auth('api')->attempt(['email' => $login, 'password' => $password, 'status' => 1]);
            if (!$token) {
                $token = auth('api')->attempt(['username' => $login, 'password' => $password, 'status' => 1]);
            }
            // Do auth
            if (!$token) {
                return $this->sendError('Unauthorized login!', ["general" => 'Unauthorized login!'], 401);
            }
            $tokenType = 'bearer';
            $expiresIn = auth('api')->factory()->getTTL();
            return $this->sendResponse('Logged in successfully!', compact('token', 'tokenType', 'expiresIn'));
        } catch (Exception $e) {
            return $this->sendError('Failed to login!', ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * Vefiry User account.",
     * @param Illuminate\Http\Request $request,
     * @return Illuminate\Http\Response $mixed,
     **/
    protected function verifyAccount(Request $request)
    {

        try {
            $postData  = $request->all();
            $validator = \Validator::make($postData, [
                'otp' => "required|exists:user,otp",
            ], [
                'otp.required' => "Password update link expired!",
                'otp.exists'   => "Invalid password update link!",
            ]);

            if ($validator->fails()) {
                return $this->sendError('Failed to verify account!', $validator->errors(), 400);
            }

            $otp = User::where([
                'otp' => $otp,
            ])->first();

            if (empty($otp)) {
                return $this->sendResponse('Failed to verify account!', ['general' => "User not found!"], 404);
            }

            \DB::beginTransaction();
            $otp->update([
                'status'        => 1,
                'otp'           => null,
                'registered_at' => \Carbon\Carbon::now(),
            ]);
            \DB::commit();
            return $this->sendResponse('Account verified successfully! Please login to your account!', "Account verified successfully! Please login to your account!");
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError('Failed to verify account!', ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * Change user password with token after login.
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response mixed
     **/
    protected function changePassword(Request $request)
    {
        try {
            $postData  = $request->all();
            $validator = \Validator::make($postData, [
                'password'              => 'required|confirmed|min:8|max:15',
                'password_confirmation' => 'min:8',
            ], [
                'password.required'              => "Please enter password!",
                'password.confirmed'             => "Please enter password same as confirmed password!",
                'password.min'                   => "Please enter password with minimum 8 characters!",
                'password_confirmation.required' => "Please enteer password!",
                'password_confirmation.min'      => "Please enter confirm password with minimum 8 characters!",
            ]);

            if ($validator->fails()) {
                return $this->sendError('Failed to update password!', $validator->errors(), 400);
            }

            $user = User::find(auth('api')->user()->id);
            if (empty($user)) {
                return $this->sendError('Failed to update password!', ['general' => "User not found!"], 404);
            }

            \DB::beginTransaction();
            $user->update([
                'password' => bcrypt($password),
            ]);
            \DB::commit();
            return $this->sendResponse('Password updated successfully!', "Password updated successfully!");
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError('Failed to update password!', ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * Get user profile base details after login.",
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response mixed
     **/
    protected function me(Request $request)
    {
        try {
            $user = \Auth::guard('api')->user();
            return $this->sendResponse('User profile retrived succesfully!', compact('user'));
        } catch (Exception $e) {
            return $this->sendError('Failed to update password!', ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * Update profile.",
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response mixed
     **/
    protected function updateProfile(Request $request)
    {
        try {
            $postData  = $request->all();
            $validator = \Validator::make($postData, [
                'email'         => "required|email|unique:user,email," . auth('api')->user()->id,
                'username'      => "required|min:4|max:20|unique:user,username," . auth('api')->user()->id,
                'firstname'     => "required|min:4|max:20",
                'lastname'      => "required|min:4|max:20",
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|dimensions:width=256,height=256',
            ], [
                'email.required'           => "Please enter email!",
                'email.email'              => "Please enter valid email!",
                'email.unique'             => "Email already occupied by another user!",
                'username.required'        => "Please enter username!",
                'username.unique'          => "User name already occupied by another user!",
                'username.min'             => "Please enter valid user name with minimum 8 characters!",
                'username.max'             => "Please enter valid user name with maximum 20 characters!",
                'firstname.required'       => "Please enter first name!",
                'firstname.min'            => "Please enter first name with minimum 4 characters!",
                'firstname.max'            => "Please enter first name with maximum 20 characters!",
                'lastname.required'        => "Please enter last name!",
                'lastname.min'             => "Please enter last name with minimum 4 characters!",
                'lastname.max'             => "Please enter last name with maximum 20 characters!",
                'profile_image.image'      => "Please select only image for profile image!",
                'profile_image.mimes'      => "Please select only jpeg, png, jpg, gif, svg file for profile image!",
                'profile_image.dimensions' => "Please select valid profile image with 256 X 256 size!",
            ]);

            if ($validator->fails()) {
                return $this->sendError('Failed to update profile!', $validator->errors(), 400);
            }
            $user = User::where([
                'id' => auth('api')->user()->id,
            ])->first();
            $dataToUpdate = [
                'username'  => $postData['username'],
                'firstname' => $postData['firstname'],
                'lastname'  => $postData['lastname'],
            ];

            if (!empty($postData['profile_image'])) {
                $imageName = CommonTrait::getOtpForUser(6) . time() . '.' . $postData['profile_image']->getClientOriginalExtension();
                $postData['profile_image']->move(public_path('profile_image'), $imageName);
                @unlink($user->profile_image);
                $dataToUpdate['profile_image'] = 'profile_image/' . $imageName;
            }

            $user->update($dataToUpdate);
            \DB::commit();
            return $this->sendResponse('Profile updated successfully!', $user);
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError('Failed to update profile!', ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * Invite user to register
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response mixed
     */
    protected function inviteUser(Request $request)
    {
        try {
            $postData  = $request->all();
            $validator = \Validator::make($postData, [
                'email' => "required|email|unique:user,email",
            ], [
                'email.required' => "Please enter email!",
                'email.email'    => "Please enter valid email!",
                'email.unique'   => "Email already registerd in our system!",
            ]);
            if ($validator->fails()) {
                return $this->sendError('Failed to invite user!', $validator->errors(), 400);
            }
            \DB::beginTransaction();
            $user = User::create([
                'email' => $postData['email'],
            ]);
            \DB::commit();
            \Mail::to($postData['email'])->send(new \App\Mail\SendInviteEmail($user));
            return $this->sendResponse('User invited successfully!', compact('user'));
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError('Failed to invite user!', ['general' => $e->getMessage()], 500);
        }
    }

    /**
     * Invite user to register
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response mixed
     */
    protected function signUp(Request $request)
    {
        try {
            $postData  = $request->all();
            $validator = \Validator::make($postData, [
                'email'                 => "required|email|exists:user,email",
                'username'              => "required|min:4|max:20|unique:user,username",
                'firstname'             => "required|min:4|max:20",
                'lastname'              => "required|min:4|max:20",
                'password'              => 'required|confirmed|min:8|max:15',
                'password_confirmation' => 'min:8',
            ], [
                'email.required'                 => "Please enter email!",
                'email.email'                    => "Please enter valid email!",
                'email.exists'                   => "You are not invited to get registered! Please contact admin to get registered!",
                'username.required'              => "Please enter username!",
                'username.unique'                => "User name already occupied by another user!",
                'username.min'                   => "Please enter valid user name with minimum 8 characters!",
                'username.max'                   => "Please enter valid user name with maximum 20 characters!",
                'firstname.required'             => "Please enter first name!",
                'firstname.min'                  => "Please enter first name with minimum 4 characters!",
                'firstname.max'                  => "Please enter first name with maximum 20 characters!",
                'lastname.required'              => "Please enter last name!",
                'lastname.min'                   => "Please enter last name with minimum 4 characters!",
                'lastname.max'                   => "Please enter last name with maximum 20 characters!",
                'password.required'              => "Please enter password!",
                'password.confirmed'             => "Please enter password same as confirmed password!",
                'password.min'                   => "Please enter password with minimum 8 characters!",
                'password.max'                   => "Please enter password with minimum 15 characters!",
                'password_confirmation.required' => "Please enteer password!",
                'password_confirmation.min'      => "Please enter confirm password with minimum 8 characters!",
            ]);

            if ($validator->fails()) {
                return $this->sendError('Failed to get registerd!', $validator->errors(), 400);
            }
            $user = User::where([
                'email' => $postData['email'],
            ])->first();
            if (!empty($user->registered_at)) {
                $errorPos = empty($user->status) ? 'verify' : 'login to';
                return $this->sendError('Failed to get registerd!', ['email' => "You have already registered your account! Please " . $errorPos . ' your account!'], 400);

            }
            do {
                $otp      = strtoupper(CommonTrait::getOtpForUser(6));
                $otpFound = \DB::table('user')->where([
                    'otp' => $otp,
                ])->count();
            } while ($otpFound != 0);
            \DB::beginTransaction();
            $user->update([
                'username'  => $postData['username'],
                'firstname' => $postData['firstname'],
                'lastname'  => $postData['lastname'],
                'otp'       => $otp,
                'password'  => bcrypt($postData['password']),
            ]);
            \DB::commit();
            \Mail::to($postData['email'])->send(new \App\Mail\SendVerifyAccountEmail($user));
            return $this->sendResponse('Account registered successfully!', 'Account registered successfully!');
        } catch (Exception $e) {
            \DB::rollback();
            return $this->sendError('Failed to get registerd!', ['general' => $e->getMessage()], 500);
        }
    }
}
