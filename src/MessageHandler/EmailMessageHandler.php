<?php

namespace App\MessageHandler;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EmailMessageHandler
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(SendEmailMessage $emailMessage): void
    {

        //$this->mailer->send($emailMessage->getMessage());
        // ... send an email!
    }
}
