<?php namespace Alerts\Controllers;

use \Symfony\Component\HttpFoundation;
use \Alerts\Services;
use \Alerts\Repositories\Interfaces;

class Login
{
    /**
     * @var \OAuth2\ResponseType\AccessToken
     */
    private $tokenGenerator;

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
     * @param \OAuth2\ResponseType\AccessToken $tokenGenerator
     * @param Interfaces\Users $userRepo
     * @param Interfaces\GitHub $github
     * @param \Monolog\Logger $logger
     */
    public function __construct(\OAuth2\ResponseType\AccessToken $tokenGenerator, Interfaces\Users $userRepo, Interfaces\GitHub $github, \Monolog\Logger $logger)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->userRepo = $userRepo;
        $this->github = $github;
        $this->logger = $logger;
    }

    public function getGitHubAuthorize(HttpFoundation\Request $request)
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

        if ($this->userRepo->save($user)) {
            //Add an access token to the user for this one time so that
            //they have something to use to contact our service again.
            $token = $this->tokenGenerator->createAccessToken('codemana', $user->id, 'user', true);

            return new HttpFoundation\JsonResponse([
                'user' => $user,
                'token' => $token
            ]);
        }

        return new HttpFoundation\Response('Failed Login', 500);
    }
}
