<?php namespace Alerts\Services\Interfaces;

interface Emailer
{
    /**
     * Send a single email for a notification
     *
     * @param string $email
     * @param string $message
     * @return boolean
     * @throws \Exception
     */
    public function send($email, $message);
}
