<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ActivationFormRequest;
use App\Http\Requests\Auth\ForgotFormRequest;
use App\Http\Requests\Auth\LoginFormRequest;
use App\Http\Requests\Auth\RegisterFormRequest;
use App\Http\Requests\Auth\ResetFormRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Throwable;
use DB;
use Faker\Core\Number;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    public function register(RegisterFormRequest $request): JsonResponse
    {
        try{
            $data = (object) $request->validated();
            DB::beginTransaction();

            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => bcrypt($data->password),
                'activation_code' => (new Number)->randomNumber(6),
            ])->assignRole(Role::findByName('customer', 'api'));

            $token = auth()->attempt(['email' => $data->email, 'password' => $data->password]);

            // dispatch(new SendUserRegisterEmail($user));

            DB::commit();
            return response()->json(['status' => true, 'token' => $token], Response::HTTP_CREATED);
        }
        catch(Throwable $th){
            DB::rollback();
            throw $th;
        }
    }

    public function activate(ActivationFormRequest $request): JsonResponse
    {
        try {
            $data = (object)$request->validated();

            $user = auth()->user();

            if ($data->activation_code === $user->activation_code) {

                DB::beginTransaction();

                $user->update([
                    'activation_code' => null,
                    'status' => 1
                ]);

                DB::commit();

                return response()->json();
            }

            throw new BadRequestException();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function login(LoginFormRequest $request): JsonResponse
    {
        try {
            $data = (object)$request->validated();

            $token = auth()->attempt(['email' => $data->email, 'password' => $data->password]);
            if($token){

                if (auth()->user()->activation_code || !auth()->user()->status) {
                    return response()->json(status: Response::HTTP_FORBIDDEN);
                }

                $user = auth()->user();
                $user->token = $token;

                return response()->json(['user' => $user]);
            }

            return response()->json(status: Response::HTTP_UNAUTHORIZED);
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public function forgot(ForgotFormRequest $request): JsonResponse
    {
        try {
            $data = (object)$request->validated();

            $token = Str::random(10);

            DB::beginTransaction();

            if (DB::table('password_resets')->where('email', $data->email)->doesntExist()) {
                DB::table('password_resets')->insert([
                    'email' => $data->email,
                    'token' => $token,
                ]);
            } else {
                DB::table('password_resets')
                    ->where('email', $data->email)
                    ->update(['token' => $token]);
            }

            // dispatch(new SendForgetPassEmail($data->email, $token));

            DB::commit();

            return response()->json();
        } catch (Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function reset(ResetFormRequest $request): JsonResponse
    {
        try {
            $data = (object) $request->validated();

            $passwordResets =
                DB::table('password_resets')
                    ->select('email')
                    ->where('token', $data->token)
                    ->first();

            $user = User::whereEmail($passwordResets?->email)->firstOrFail();

            DB::beginTransaction();

            $user->update([
                'password' => bcrypt($data->password)
            ]);

            DB::commit();

            return response()->json();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
