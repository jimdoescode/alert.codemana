<?php namespace Alerts\Controllers;

use \Symfony\Component\HttpFoundation;
use \Symfony\Component\Templating;

class Home
{
    /**
     * @var Templating\PhpEngine
     */
    private $view;

    /**
     * @var string
     */
    private $githubClientId;

    /**
     * @var \Monolog\Logger
     */
    private $log;

    /**
     * Home constructor.
     * @param Templating\PhpEngine $view
     * @param string $githubClientId
     * @param \Monolog\Logger $log
     */
    public function __construct(Templating\PhpEngine $view, $githubClientId, \Monolog\Logger $log)
    {
        $this->view = $view;
        $this->githubClientId = $githubClientId;
        $this->log = $log;
    }

    public function getIndex(HttpFoundation\Request $request)
    {
        return $this->view->render('mainPage', [
            'githubClientId' => $this->githubClientId,
            'loggedIn' => !is_null($request->get('user'))
        ]);
    }
}
