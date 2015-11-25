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
     * Hook constructor.
     * @param Services\GitHub $githubService
     * @param Services\Interfaces\Emailer $emailerService
     */
    public function __construct(Services\GitHub $githubService, Services\Interfaces\Emailer $emailerService)
    {
        $this->githubService = $githubService;
        $this->emailerService = $emailerService;
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

        $body = '';
        foreach ($files as $file) {
            $body .= $file['patch'];
        }

        $this->emailerService->send('jimdoescode@gmail.com', $body);

        return new \Symfony\Component\HttpFoundation\Response('Hello Silex', 202);
    }
}
