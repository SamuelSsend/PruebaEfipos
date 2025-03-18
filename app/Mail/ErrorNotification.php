<?php

namespace App\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $exception;
    public $url;

    /**
     * Create a new message instance.
     *
     * @param Exception $exception
     * @return void
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
        $this->url = config('app.url');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Error 500 en la aplicación')
                    ->view('emails.error_notification')
                    ->with([
                        'exceptionMessage' => $this->exception->getMessage(),
                        'exceptionFile'    => $this->exception->getFile(),
                        'exceptionLine'    => $this->exception->getLine(),
                        'exceptionTrace'   => $this->exception->getTraceAsString(),
                        'appUrl'           => $this->url,
                    ]);
    }
}
