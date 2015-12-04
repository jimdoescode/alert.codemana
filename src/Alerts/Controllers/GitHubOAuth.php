<?php namespace Alerts\Controllers;

use \Symfony\Component\HttpFoundation;
use \Alerts\Services;
use \Alerts\Repositories\Interfaces;

class GitHubOAuth
{
    /**
     * @var Interfaces\GitHub
     */
    private $github;

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @param Interfaces\GitHub $github
     * @param \Monolog\Logger $logger
     */
    public function __construct(Interfaces\GitHub $github, \Monolog\Logger $logger)
    {
        $this->github = $github;
        $this->logger = $logger;
    }

    public function getAuthorize(HttpFoundation\Request $request)
    {
        $code = $request->get('code');
        if (is_null($code)) {
            return $this->github->getAuthorizationRedirect();
        }

        $user = $this->github->getUserFromOAuth($code);
        return new HttpFoundation\JsonResponse($user);
    }
}
