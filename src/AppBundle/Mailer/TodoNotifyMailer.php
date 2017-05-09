<?php

namespace AppBundle\Mailer;

use AppBundle\Entity\Todo;
use Symfony\Bundle\TwigBundle\TwigEngine;

class TodoNotifyMailer
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var TwigEngine
     */
    private $template;

    /**
     * Create a new Mail for delivery.
     *
     * @param Swift_Mailer                         $mailer
     * @param Symfony\Bundle\TwigBundle\TwigEngine $template
     */
    public function __construct(\Swift_Mailer $mailer, TwigEngine $template)
    {
        $this->mailer = $mailer;
        $this->template = $template;
    }

    /**
     * Send Todo Email Notification to user.
     *
     * @param AppBundle\Entity\Todo $todo
     *
     * @return int
     */
    public function sendEmail(Todo $todo)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Hey! You must do sometning')
            ->setFrom('no-reply@example.com')
            ->setTo($todo->getUser()->getEmail())
            ->setBody(
                $this->template->render(
                    'emails/todoNotification.html.twig',
                    [
                        'username' => $todo->getUser()->getUsername(),
                        'content'  => $todo->getContent(),
                        'dueDate'  => $todo->getDueDate(),
                    ]
                ),
                'text/html'
            );

        return $this->mailer->send($message);
    }
}
