<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        try {
            //24小时相同url只收一次
            if (strlen(env('ERROR_MAIL', '')) > 0) {
                $key = 'spider_error_' . md5($request);
                if (!Redis::exists($key)) {
                    Redis::set($key, '1');
                    Redis::expire($key, 60 * 60 * 24);
                    $mails = env('ERROR_MAIL', '');
                    $mails = explode(',', $mails);
                    //发送爬虫错误邮件
                    foreach ($mails as $mail) {
                        Mail::raw(date_format(date_create(), 'Y-m-d H:i') . ' 的时候 ' . $request->url() . ' 出错了', function ($message) use ($exception, $mail) {
                            //暂时只发给自己先
                            $message->to($mail, '开发者')
                                ->subject($exception->getMessage());
                        });
                    }
                }
            }
        }
        catch (Exception $e){
            return parent::render($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}
