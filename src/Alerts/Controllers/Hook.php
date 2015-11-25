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
     * Hook constructor.
     * @param Services\GitHub $githubService
     */
    public function __construct(Services\GitHub $githubService)
    {
        $this->githubService = $githubService;
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

        return new \Symfony\Component\HttpFoundation\Response('Hello Silex', 202);
    }
}
