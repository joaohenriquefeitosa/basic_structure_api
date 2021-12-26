<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $th, Request $request) {
            $this->logException($th, $request);
            return response()->json(status: Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (ModelNotFoundException $th, Request $request) {
            $this->logException($th, $request);
            return response()->json(status: Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (MethodNotAllowedHttpException $th, Request $request) {
            $this->logException($th, $request);
            return response()->json(status: Response::HTTP_METHOD_NOT_ALLOWED);
        });

        $this->renderable(function (ValidationException $th, Request $request) {
            $this->logException($th, $request, ['errors' => $th->errors()]);
            return response()->json($th->errors(),Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $this->renderable(function (RequestException $th, Request $request) {
            $this->logException($th, $request, ['errors' => $th->getErrors()]);
            return response()->json($th->getErrors(),Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $this->renderable(function (UnauthorizedException $th, Request $request) {
            $this->logException($th, $request);
            return response()->json(status: Response::HTTP_FORBIDDEN);
        });

        $this->renderable(function (BadRequestException $th, Request $request) {
            $this->logException($th, $request);
            return response()->json(status: Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (Throwable $th, Request $request) {
            $this->logException($th, $request);
            return response()->json(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }

    private function logException (Throwable $th, Request $request, array $extras = []) {
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'type' => $th::class,
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'request' => $request->toArray(),
                'path' => $request->path(),
                'extras' => $extras
            ])
            ->log($th->getMessage());
    }
}
