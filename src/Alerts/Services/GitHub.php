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
     * @param string $owner
     * @param string $repo
     * @param string $base
     * @param string $head
     * @param array $statusFilter
     * @return array
     */
    public function filesChangedInPush($owner, $repo, $base, $head, $statusFilter = [])
    {
        $response = $this->client->get("/repos/{$owner}/{$repo}/{$base}...{$head}");
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $comparison = $response->json();

            return array_filter($comparison['files'], function ($file) use ($statusFilter) {
                return in_array($file['status'], $statusFilter);
            });
        }
        return [];
    }
}
