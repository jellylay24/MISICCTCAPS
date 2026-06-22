<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\Http;

class BrevoMailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->resolving('mail.manager', function (MailManager $mailManager) {
            $mailManager->extend('brevo', function () {
                $apiKey = config('mail.mailers.brevo.key') ?? env('BREVO_API_KEY');
                return new class($apiKey) extends AbstractTransport {
                    private string $apiKey;

                    public function __construct(string $apiKey)
                    {
                        parent::__construct();
                        $this->apiKey = $apiKey;
                    }

                    protected function doSend(SentMessage $message): void
                    {
                        // Not used — we override send() directly
                    }

                    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
                    {
                        if (!($message instanceof Email)) {
                            throw new \RuntimeException('Only Email messages are supported');
                        }

                        $toAddresses = $message->getTo();
                        $to = [];
                        foreach ($toAddresses as $addr) {
                            $name = $addr->getName() ?: 'User';
                            $to[] = ['email' => $addr->getAddress(), 'name' => $name];
                        }

                        // Read sender from config/mail.php (which reads from .env)
                        $mailFrom = config('mail.from');
                        $sender = [
                            'email' => $mailFrom['address'] ?? 'cydmdalupan@gmail.com',
                            'name' => $mailFrom['name'] ?? 'ICCT-MIS',
                        ];

                        $subject = $message->getSubject() ?? 'No Subject';

                        $htmlBody = $message->getHtmlBody();
                        if (!$htmlBody) {
                            $textBody = $message->getTextBody();
                            $htmlBody = $textBody ? nl2br(e($textBody)) : '';
                        }

                        $payload = [
                            'sender' => $sender,
                            'to' => $to,
                            'subject' => $subject,
                            'replyTo' => $sender,
                        ];

                        if ($htmlBody) {
                            $payload['htmlContent'] = $htmlBody;
                        }

                        $attachments = $message->getAttachments();
                        $brevoAttachments = [];
                        foreach ($attachments as $attachment) {
                            $brevoAttachments[] = [
                                'content' => base64_encode($attachment->getBody()),
                                'name' => $attachment->getFilename() ?? 'attachment',
                            ];
                        }
                        if (!empty($brevoAttachments)) {
                            $payload['attachment'] = $brevoAttachments;
                        }

                        $response = Http::withHeaders([
                            'api-key' => $this->apiKey,
                            'Content-Type' => 'application/json',
                        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

                        if (!$response->successful()) {
                            throw new \RuntimeException(
                                'Brevo API error: ' . $response->body()
                            );
                        }

                        return new SentMessage($message, $envelope ?? new Envelope(
                            new \Symfony\Component\Mime\Address($sender['email'], $sender['name']),
                            iterator_to_array($toAddresses)
                        ));
                    }

                    public function __toString(): string
                    {
                        return 'brevo';
                    }
                };
            });
        });
    }
}
