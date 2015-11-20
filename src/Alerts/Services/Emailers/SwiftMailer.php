<?php namespace Alerts\Services\Emailers;

class SwiftMailer implements \Alerts\Services\Interfaces\Emailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var array
     */
    private $from;

    /**
     * SwiftMailer constructor.
     * @param \Swift_Mailer $mailer
     * @param array $from
     */
    public function __construct(\Swift_Mailer $mailer, array $from)
    {
        $this->mailer = $mailer;
        $this->from = $from;
    }

    public function send($email, $body)
    {
        $message = \Swift_Message::newInstance();
        $message->setSubject('New Notification from Relay GSE');
        $message->setFrom($this->from);
        $message->setTo($email);
        $message->setBody($body, 'text/html');
        $message->addPart($body, 'text/plain');

        return $this->mailer->send($message);
    }
}
