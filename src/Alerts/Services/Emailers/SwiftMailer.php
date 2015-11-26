<?php namespace Alerts\Services\Emailers;

use Symfony\Component\Templating;

class SwiftMailer implements \Alerts\Services\Interfaces\Emailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Templating\PhpEngine
     */
    private $view;

    /**
     * @var array
     */
    private $from;

    /**
     * SwiftMailer constructor.
     * @param \Swift_Mailer $mailer
     * @param array $from
     */
    public function __construct(\Swift_Mailer $mailer, array $from, Templating\PhpEngine $view)
    {
        $this->mailer = $mailer;
        $this->from = $from;
        $this->view = $view;
    }

    public function send($email, $patchFiles)
    {
        $message = \Swift_Message::newInstance();
        $message->setSubject('CodeMana Alert');
        $message->setFrom($this->from);
        $message->setTo($email);
        $message->setBody($this->view->render('htmlEmail', ['patchFiles' => $patchFiles]), 'text/html');
        //$message->addPart($body, 'text/plain');

        return $this->mailer->send($message);
    }
}
