<?php namespace Alerts\Controllers;

use \Symfony\Component\HttpFoundation;
use \Alerts\Services;
use \Alerts\Repositories\Interfaces;

class GitHubLogin
{
    /**
     * @var Interfaces\Users
     */
    private $userRepo;

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
    public function __construct(Interfaces\Users $userRepo, Interfaces\GitHub $github, \Monolog\Logger $logger)
    {
        $this->userRepo = $userRepo;
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
        //Check to see if we already have this user. If so then set their
        //ID so we update the user instead of creating a new one.
        $dbUser = $this->userRepo->getAll(['githubId' => $user->githubId], 1);
        if (!empty($dbUser)) {
            $user->id = $dbUser[0]->id;
        }

        $this->userRepo->save($user);
        return new HttpFoundation\JsonResponse($user);
    }
}
