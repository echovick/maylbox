<?php

namespace App\Services;

use App\Models\EmailAccount;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SmtpService
{
    /**
     * Send an email using the account's SMTP credentials.
     *
     * @return string The generated Message-ID
     */
    public function send(EmailAccount $account, array $data): string
    {
        $dsn = $this->buildDsn($account);
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);

        $fromName = $data['from_name'] ?? $account->name ?? '';

        $email = (new Email())
            ->from(new Address($account->email, $fromName))
            ->subject($data['subject'] ?? '(No Subject)');

        // To recipients
        foreach ($data['to'] ?? [] as $recipient) {
            $email->addTo(new Address($recipient['email'], $recipient['name'] ?? ''));
        }

        // Cc recipients
        foreach ($data['cc'] ?? [] as $recipient) {
            $email->addCc(new Address($recipient['email'], $recipient['name'] ?? ''));
        }

        // Bcc recipients
        foreach ($data['bcc'] ?? [] as $recipient) {
            $email->addBcc(new Address($recipient['email'], $recipient['name'] ?? ''));
        }

        // Body
        if (!empty($data['body_html'])) {
            $email->html($data['body_html']);
            $email->text(strip_tags($data['body_html']));
        }

        // In-Reply-To header
        if (!empty($data['in_reply_to'])) {
            $email->getHeaders()->addIdHeader('In-Reply-To', $data['in_reply_to']);
        }

        $mailer->send($email);

        // Extract the Message-ID that Symfony generated
        $messageId = $email->getHeaders()->get('Message-ID')?->getBodyAsString() ?? '';
        $messageId = trim($messageId, '<>');

        return $messageId;
    }

    private function buildDsn(EmailAccount $account): string
    {
        $scheme = $account->smtp_encryption === 'ssl' ? 'smtps' : 'smtp';
        $username = urlencode($account->smtp_username ?: $account->email);
        $password = urlencode($account->smtp_password);
        $host = $account->smtp_host;
        $port = $account->smtp_port;

        return "{$scheme}://{$username}:{$password}@{$host}:{$port}";
    }
}
