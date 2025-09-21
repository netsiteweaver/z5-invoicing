<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
	use Queueable, SerializesModels;

	public function __construct(public string $subjectText, public string $bodyText)
	{
	}

	public function build()
	{
		$bodyHtml = nl2br(e($this->bodyText));
		return $this->subject($this->subjectText)
			->view('emails.html_generic', [
				'bodyHtml' => $bodyHtml,
			])
			->text('emails.plain_generic', [
				'body' => $this->bodyText,
			]);
	}
}

