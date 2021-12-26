<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests\User\ShowFormRequest;
use App\Http\Requests\User\IndexFormRequest;
use App\Http\Requests\User\StoreFormRequest;
use App\Http\Requests\User\UpdateFormRequest;
use App\Http\Requests\User\DeleteFormRequest;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use DB;

use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(IndexFormRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = User::getUsers($data);
        
        return response()->json($result);
    }

    public function show(ShowFormRequest $request): JsonResponse
    {
        try {
            $data = (object) $request->validated();

            $user = User::findOrFail($data->id);

            return response()->json($user);
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public function store(StoreFormRequest $request): JsonResponse
    {
        try{
            $data = (object)$request->validated();

            DB::beginTransaction();

            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => bcrypt($data->password)
            ])->assignRole(Role::findByName($data->role, 'api'));

            if($request->hasFile('avatar')){

                if (!in_array(
                    $request->file('avatar')->extension(),
                    array_merge(User::IMAGE_MIMES)
                )) {
                    throw new BadRequestException('Not supported media type was uploaded');
                }

                $fileName = uniqid() . "." .$request->file('avatar')->extension();

                $request->file('avatar')->storeAs(User::STORAGE_PATH, $fileName);

                $user->update([
                    'avatar' => $fileName
                ]);
            }

            DB::commit();

            return response()->json(status: Response::HTTP_CREATED);
        }catch (Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function update(UpdateFormRequest $request): JsonResponse
    {
        try{
            $data = (object)$request->validated();

            $user = User::findOrFail($data->id);

            if(auth()->id() != $data->id && !auth()->user()->hasRole(['admin'])) {
                throw new UnauthorizedException(Response::HTTP_FORBIDDEN);
            }

            DB::beginTransaction();

            if($request->hasFile('avatar')){

                if (!in_array(
                    $request->file('avatar')->extension(),
                    array_merge(User::IMAGE_MIMES)
                )) {
                    throw new BadRequestException('Not supported media type was uploaded');
                }

                $fileName = uniqid() . "." .$request->file('avatar')->extension();

                $request->file('avatar')->storeAs(User::STORAGE_PATH, $fileName);

                $user->update([
                    'avatar' => $fileName
                ]);
            }

            $user->update([
                'name' => object_get($data, 'name', $user->name),
                'status' => object_get($data, 'status', $user->status),
            ]);

            DB::commit();

            return response()->json();
        }catch (Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function destroy(DeleteFormRequest $request): JsonResponse
    {
        try{
            $data = (object)$request->validated();

            $user = User::findOrFail($data->id);

            DB::beginTransaction();

            $user->delete();

            DB::commit();

            return response()->json(status: Response::HTTP_NO_CONTENT);
        }catch (Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
}
