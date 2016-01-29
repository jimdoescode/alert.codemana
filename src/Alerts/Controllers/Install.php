<?php namespace Alerts\Controllers;

use \Symfony\Component\HttpFoundation;
use \Alerts\Services;
use \Alerts\Models;
use \Alerts\Repositories\Interfaces;

class Install
{
    /**
     * @var Interfaces\GitHub
     */
    private $githubRepo;

    /**
     * @var Interfaces\WatchedRepos
     */
    private $watchedReposRepository;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * Install constructor.
     * @param Interfaces\GitHub $githubRepo
     * @param Interfaces\WatchedRepos $watchedReposRepository
     * @param string $baseUrl
     * @param \Monolog\Logger $logger
     */
    public function __construct(Interfaces\GitHub $githubRepo,
                                Interfaces\WatchedRepos $watchedReposRepository,
                                $baseUrl,
                                \Monolog\Logger $logger)
    {
        $this->githubRepo = $githubRepo;
        $this->watchedReposRepository = $watchedReposRepository;
        $this->baseUrl = $baseUrl;
        $this->logger = $logger;
    }

    public function postGithub(HttpFoundation\Request $request)
    {
        //$rawContent = $request->getContent();
        //$repoContent = json_decode($rawContent, true);

        $repo = new Models\Repo();
        //$repo->name = $repoContent['name'];
        $repo->name = 'jimdoescode/alert.codemana.com';

        //This should come from the OAuth token.
        $user = new Models\User();
        $user->githubAccessToken = 'dd800d1fd1d71866328d4e78bb42684509eb73c4';

        $this->githubRepo->installHook(
            $user,
            $this->watchedReposRepository->createNew($repo),
            $this->baseUrl
        );
    }
}
