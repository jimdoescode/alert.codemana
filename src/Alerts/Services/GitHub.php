<?php namespace Alerts\Services;

class GitHub
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * GitHub constructor.
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }

    /**
     * Calls the route `/repos/:owner/:repo/compare/:base...:head` and returns all the files
     * with a status that matches the specified status filter.
     *
     * @param string $owner
     * @param string $repo
     * @param string $base
     * @param string $head
     * @param array $statusFilter
     * @return array
     */
    public function filesChangedInPush($owner, $repo, $base, $head, $statusFilter = [])
    {
        $response = $this->client->get("/repos/{$owner}/{$repo}/compare/{$base}...{$head}");
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $comparison = json_decode($response->getBody(), true);

            return array_filter($comparison['files'], function ($file) use ($statusFilter) {
                return in_array($file['status'], $statusFilter);
            });
        }
        return [];
    }
}
