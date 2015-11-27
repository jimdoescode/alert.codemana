<?php namespace Alerts\Controllers;

use \Symfony\Component\HttpFoundation;
use \Alerts\Services;

class Hook
{
    /**
     * @var Services\GitHub
     */
    private $githubService;

    /**
     * @var Services\Interfaces\Emailer
     */
    private $emailerService;

    /**
     * @var Services\Converter
     */
    private $converterService;

    /**
     * Hook constructor.
     * @param Services\GitHub $githubService
     * @param Services\Interfaces\Emailer $emailerService
     * @param Services\Converter $converterService
     */
    public function __construct(Services\GitHub $githubService, Services\Interfaces\Emailer $emailerService, Services\Converter $converterService)
    {
        $this->githubService = $githubService;
        $this->emailerService = $emailerService;
        $this->converterService = $converterService;
    }

    public function postIndex(HttpFoundation\Request $request)
    {
        $hookContent = json_decode($request->getContent(), true);
        $filters = ['modified', 'removed'];

        $files = $this->githubService->filesChangedInPush(
            $hookContent['repository']['owner']['name'],
            $hookContent['repository']['name'],
            $hookContent['before'],
            $hookContent['after'],
            $filters
        );

        //Create an array of [edited-filename => [authors...]]
        $fileEditors = [];
        foreach ($hookContent['commits'] as $commit) {
            foreach ($filters as $filter) {
                foreach ($commit[$filter] as $file) {
                    if (!array_key_exists($file, $fileEditors)) {
                        $fileEditors[$file] = [];
                    }
                    $fileEditors[] = $commit['committer']['name'];
                }
            }
        }

        $patchModels = [];
        foreach ($files as $file) {
            $patchModels[] = $this->converterService->patchToModel($file['filename'], $file['patch'], $fileEditors[$file['filename']]);
        }

        $this->emailerService->send('jimdoescode@gmail.com', $patchModels);

        //202 means accepted but processing hasn't started yet. Perhaps we
        //could offload the work from the server to some other worker process.
        return new \Symfony\Component\HttpFoundation\Response('Hello GitHub', 202);
    }
}
