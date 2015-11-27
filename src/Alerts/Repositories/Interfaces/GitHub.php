<?php namespace Alerts\Repositories\Interfaces;

use \Alerts\Models;

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
}
