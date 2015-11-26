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

        $files = $this->githubService->filesChangedInPush(
            $hookContent['repository']['owner']['name'],
            $hookContent['repository']['name'],
            $hookContent['before'],
            $hookContent['after'],
            ['modified', 'removed']
        );

        $patchModels = [];
        foreach ($files as $file) {
            $patchModels[] = $this->converterService->patchToModel($file['filename'], $file['patch']);
        }

        $this->emailerService->send('jimdoescode@gmail.com', $patchModels);

        return new \Symfony\Component\HttpFoundation\Response('Hello GitHub', 202);
    }
}
