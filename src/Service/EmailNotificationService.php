<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationService
{
    public function __construct(private MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    public function sendEmail(string $receiver, array $case): ?string
    {
        try {
            $email = (new TemplatedEmail())  // Email avec template Twig
                ->from('hello@codexpress.fr')
                ->to($receiver)
                ->subject($case['subject'])
                ->priority(Email::PRIORITY_HIGH)
                ->htmlTemplate('email/'. $case['template'] .'.html.twig');
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                
                if ($case === 'premium') {
                    $email
                        ->subject('Thank you for your purchase!')
                        ->priority(Email::PRIORITY_HIGH)
                        ->htmlTemplate('email/base.html.twig');
                } elseif ($case === 'registration') {
                    $email
                        ->subject('Welcome to CodeXpress, explore a new way of sharing code !')
                        ->priority(Email::PRIORITY_LOW)
                        ->htmlTemplate('email/welcome.html.twig');
                }

                $this->mailer->send($email);
                return 'Email sent';
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        // symfony console create:service Test == Créer un service
    }

}