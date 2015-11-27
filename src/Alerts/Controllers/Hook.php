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
     * Hook constructor.
     * @param Interfaces\GitHub $githubRepo
     * @param Services\Interfaces\Emailer $emailerService
     */
    public function __construct(Interfaces\GitHub $githubRepo, Services\Interfaces\Emailer $emailerService)
    {
        $this->githubRepo = $githubRepo;
        $this->emailerService = $emailerService;
    }

    public function postIndex(HttpFoundation\Request $request)
    {
        $signature = $request->headers->get('X-Hub-Signature');
        $hookContent = json_decode($request->getContent(), true);

        //Check the secret.
        if ($signature !== hash_hmac('sha1', $hookContent, 'super_secret_key_thingy')) {
            return new HttpFoundation\Response('You\'re not GitHub', 405);
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
