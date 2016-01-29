<?php namespace Alerts\Controllers;

use \Symfony\Component\HttpFoundation;
use \Alerts\Services;
use \Alerts\Repositories\Interfaces;

class Hook
{
    /**
     * @var Interfaces\GitHub
     */
    private $githubRepo;

    /**
     * @var Services\Interfaces\Emailer
     */
    private $emailerService;

    /**
     * @var Interfaces\WatchedRepos
     */
    private $watchedReposRepository;

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * Hook constructor.
     * @param Interfaces\GitHub $githubRepo
     * @param Services\Interfaces\Emailer $emailerService
     * @param Interfaces\WatchedRepos $watchedReposRepository
     */
    public function __construct(Interfaces\GitHub $githubRepo,
                                Services\Interfaces\Emailer $emailerService,
                                Interfaces\WatchedRepos $watchedReposRepository,
                                \Monolog\Logger $logger)
    {
        $this->githubRepo = $githubRepo;
        $this->emailerService = $emailerService;
        $this->watchedReposRepository = $watchedReposRepository;
        $this->logger = $logger;
    }

    public function postGitHub(HttpFoundation\Request $request)
    {
        $signature = $request->headers->get('X-Hub-Signature');
        $rawContent = $request->getContent();
        $hookContent = json_decode($rawContent, true);

        $watchedRepo = $this->watchedReposRepository->getById($hookContent['repository']['id']);

        if ($signature !== ('sha1=' . hash_hmac('sha1', $rawContent, $watchedRepo->secret))) {
            return new HttpFoundation\Response('You\'re not GitHub', 403);
        }

        //If a field called hook exists in the content sent
        //to us then we'll say it's installed.
        if (isset($hookContent['hook'])) {
            return new HttpFoundation\Response('Hook Installed', 202);
        }

        $filters = ['modified', 'removed'];

        //Create an array of [filename => [editors...]]
        $fileEditors = [];
        foreach ($hookContent['commits'] as $commit) {
            foreach ($filters as $filter) {
                foreach ($commit[$filter] as $file) {
                    if (!array_key_exists($file, $fileEditors)) {
                        $fileEditors[$file] = [];
                    }
                    if (!in_array($commit['committer']['name'], $fileEditors[$file])) {
                        $fileEditors[$file][] = $commit['committer']['name'];
                    }
                }
            }
        }

        $patchModels = $this->githubRepo->getChangePatches(
            $hookContent['repository']['full_name'],
            $hookContent['before'],
            $hookContent['after'],
            $fileEditors,
            $filters
        );

        $this->emailerService->send('jimdoescode@gmail.com', $patchModels);

        //202 means accepted but processing hasn't started yet. Perhaps we
        //could offload the work from the server to some other worker process.
        return new HttpFoundation\Response('Hello GitHub', 202);
    }
}
