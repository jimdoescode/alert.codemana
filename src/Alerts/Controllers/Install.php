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
    private $log;

    /**
     * Install constructor.
     * @param Interfaces\GitHub $githubRepo
     * @param Interfaces\WatchedRepos $watchedReposRepository
     * @param string $baseUrl
     * @param \Monolog\Logger $log
     */
    public function __construct(Interfaces\GitHub $githubRepo,
                                Interfaces\WatchedRepos $watchedReposRepository,
                                $baseUrl,
                                \Monolog\Logger $log)
    {
        $this->githubRepo = $githubRepo;
        $this->watchedReposRepository = $watchedReposRepository;
        $this->baseUrl = $baseUrl;
        $this->log = $log;
    }

    /**
     * @param HttpFoundation\Request $request
     * @return HttpFoundation\Response
     */
    public function postGitHub(HttpFoundation\Request $request)
    {
        $rawContent = $request->getContent();
        $repoContent = json_decode($rawContent, true);

        $watchedRepo = $this->watchedReposRepository->createNew($repoContent['name']);

        //This should come from the OAuth token.
        $user = $request->get('user');
        $success = $this->githubRepo->installHook(
            $user,
            $watchedRepo,
            $this->baseUrl
        );

        if ($success) {
            return $this->watchedReposRepository->save($watchedRepo) ?
                new HttpFoundation\JsonResponse($watchedRepo, 201) :
                new HttpFoundation\Response('Failed to Save', 507);
        }

        return new HttpFoundation\Response('GitHub Request Failed', 502);
    }

    /**
     * Options request for any Installation request.
     *
     * @param HttpFoundation\Request $request
     * @return HttpFoundation\Response
     */
    public function optionsIndex(HttpFoundation\Request $request)
    {
        $this->log->addDebug(print_r($request, true), [
            'namespace' => 'Alerts\\Controllers\\Install',
            'method' => 'optionsIndex',
            'type' => 'request'
        ]);

        $response = new HttpFoundation\Response('OK');
        $response->headers->add([
            'Access-Control-Allow-Methods' => 'POST, OPTIONS',
        ]);

        return $response;
    }
}
