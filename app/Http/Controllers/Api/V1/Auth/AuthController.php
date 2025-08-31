<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ConfirmEmailOtpAndVerifyEmailRequest;
use App\Http\Requests\Api\V1\Auth\ForgetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\LogoutRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\VerifyEmailCodeRequest;
use App\Http\Requests\Api\V1\Auth\VerifyOtpAndReset;
use App\Models\User;
use App\Notifications\SendEmailVerificationOtpNotification;
use App\Notifications\SendPasswordOtpNotification;
use App\Services\AuthService;
use App\Services\UserService;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;
    // inject the authService obj to call the register function
    public function __construct(private AuthService $authService, private UserService $userService) {}



    public function register(RegisterRequest $request)
    {
        try {

            //create new user from createUser fun in UserService class
            $new_user = $this->userService->createUser($request);

            if (empty($new_user)) {
                return $this->errorResponse(
                    message: 'user not created !',
                    code: 400,
                    errors: __('auth.field_to_create_token')
                );
            }

            return $this->successResponse([
                'user'  => $new_user,
            ], __('auth.register_success'), 201);
        } catch (\Throwable $e) {

            Log::error('Register Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(),]);

            return response()->json([
                'error entier server . "AuthController"',
                500,
                __($e->getMessage()),
            ], 501);
        }
    }

    public function login(LoginRequest $request)
    {
        try {

            // check if the email verifited
            // if (!($this->authService->checkIfEmailVerifited($request->email))) {
            //     return $this->errorResponse(
            //         __('auth.email_not_verified'),
            //         403,
            //         []
            //     );
            // }

            // check email and password from login fun in AuthService class
            // call it if the email verifited
            $result = $this->authService->login($request->all());

            if (!$result) {
                return $this->errorResponse(
                    __('auth.invalid_credentials'),
                    401,
                    ['credentials' => __('auth.invalid_credentials_details')]
                );
            }


            return $this->successResponse(
                [
                    'user'  => $result['user'],
                    'token' => $result['token'],
                ],
                __('auth.login_success')
            );
        } catch (\Throwable $e) {
            Log::error('Login Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return $this->errorResponse(
                __('auth.server_error'),
                500,
                ['exception' => $e->getMessage()]
            );
        }
    }

    public function logout(LogoutRequest $request)
    {
        try {

            $user = $request->user();
            if ($user && $user->token()) {
                $user->token()->revoke();

                return $this->successResponse(
                    null,
                    __('auth.logout_success')
                );
            }

            return $this->errorResponse(
                __('auth.not_authenticated'),
                401
            );
        } catch (\Throwable $e) {
            Log::error('Logout Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return $this->errorResponse(
                __('auth.server_error'),
                500,
                ['exception' => $e->getMessage()]
            );
        }
    }

    public function me(Request $request)
    {

        return $this->successResponse(
            $request->user(),
            __('auth.authenticated_user')
        );
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $status = Password::reset(
                $request->only('email', 'password', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                }
            );
            // return $this->successResponse($status . "   " . Password::PASSWORD_RESET, "mess" . $status === Password::PASSWORD_RESET);

            return $status === Password::PASSWORD_RESET
                ? $this->successResponse(null, __('auth.password_reset_success'))
                : $this->errorResponse(__('auth.password_reset_failed'), 422);
        } catch (\Throwable $e) {
            Log::error('Reset Password Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return $this->errorResponse(
                __('auth.server_error'),
                500,
                ['exception' => $e->getMessage()]
            );
        }
    }


    // accept email and create opt code to sent it for user email
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        try {
            // create and store the otp code
            $otp = $this->authService->createOtp('password_otps', $request->email);

            // get the email and send the otp code by email
            $user = User::where('email', $request->email)->first();
            $user->notify(new SendPasswordOtpNotification($otp));

            return $this->successResponse(null, __('auth.otp_sent'));
        } catch (\Throwable $e) {
            Log::error('ForgetPassword OTP Error: ' . $e->getMessage());

            return $this->errorResponse(__('auth.server_error'), 500);
        }
    }

    // accept email, new_password, otp_code then compare the otp code that was sent to user email with the db_otp_code
    public function verifyOtpAndResetPassword(VerifyOtpAndReset $request)
    {

        // verify Otp
        // table('password_otps')
        $otp = $this->authService->verifiyOtp(
            'password_otps',
            $request->email,
            $request->otp_code
        );

        if (!$otp) {
            return $this->errorResponse(__('auth.invalid_or_expired_otp'), 422);
        }

        // update the password by new password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // update the otp sateus to used
        DB::table('password_otps')->where('id', $otp->id)->update(['used' => true]);

        return $this->successResponse(null, __('auth.password_reset_success'));
    }


    //accept email, password and sent the otp_code to the email
    public function verifyEmailCode(VerifyEmailCodeRequest $request)
    {

        //get user
        $user = User::where('email', $request->email)->first();

        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse(__('auth.invalid_credentials'), 401);
        }

        //check if email is verified
        if ($user->email_verified_at) {
            return $this->errorResponse(__('auth.email_already_verified'), 422);
        }

        // table('email_verifications')
        //create otp and store it in db
        $otp = $this->authService->createOtp('email_otps', $request->email);

        //send the otp_code to the email
        $user->notify(new SendEmailVerificationOtpNotification($otp));

        return $this->successResponse(null, __('auth.otp_sent_to_email'));
    }

    // accept email, otp_code and compar the otp_code with otp in the db and verifiy if correct
    public function confirmEmailOtpAndVerifyEmail(ConfirmEmailOtpAndVerifyEmailRequest $request)
    {

        $otp = $this->authService->verifiyOtp('email_otps', $request->email, $request->otp_code);


        if (!$otp) {
            return $this->errorResponse(__('auth.invalid_or_expired_otp'), 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->email_verified_at = now();
        $user->save();

        DB::table('email_otps')
            ->where('id', $otp->id)
            ->update(['used' => true]);

        return $this->successResponse(null, __('auth.email_verified_successfully'));
    }
}
