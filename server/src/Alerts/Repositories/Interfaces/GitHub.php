<?php namespace Alerts\Repositories\Interfaces;

use \Alerts\Models;
use \Symfony\Component\HttpFoundation;

interface GitHub
{
    /**
     * Calls the route `/repos/:owner/:repo/compare/:base...:head` and returns all the patches
     * with a status that matches the specified status filter.
     *
     * @param string $repo
     * @param string $base
     * @param string $head
     * @param array $fileEditors [filename => [editors...]]
     * @param array $statusFilter
     * @return Models\PatchFile[]
     */
    public function getChangePatches($repo, $base, $head, $fileEditors, $statusFilter = []);

    /**
     * POSTs to https://github.com/login/oauth/access_token to get an access token from the code. Then gets
     * data to construct a user model
     *
     * @param $code
     * @param $state
     * @return Models\User
     */
    public function getUserFromOAuth($code, $state);

    /**
     * Installs the hook
     *
     * @param Models\User $user
     * @param Models\WatchedRepo $repo
     * @param $callbackUrl
     * @return bool
     */
    public function installHook(Models\User $user, Models\WatchedRepo $repo, $callbackUrl);
}
