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
        $response = $this->retryWithExponentialBackoff(3, function () use ($owner, $repo, $base, $head) {

            return $this->client->get("/repos/{$owner}/{$repo}/compare/{$base}...{$head}");

        });

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $comparison = json_decode($response->getBody(), true);

            return array_filter($comparison['files'], function ($file) use ($statusFilter) {
                return in_array($file['status'], $statusFilter);
            });
        }
        return [];
    }

    /**
     * Runs a closure until it succeeds or the maximum number of attempts is reached.
     * There is an exponential back off (sleep) in seconds for each failed attempt.
     *
     * @param int $attempts
     * @param callable $retry
     * @return mixed
     * @throws \Exception
     */
    private function retryWithExponentialBackoff($attempts, callable $retry)
    {
        for ($i = 1; $i <= $attempts; $i++) {
            try {

                return $retry();

            } catch (\Exception $e) {

                if ($i === $attempts) {
                    //We failed so throw the exception to the caller
                    throw $e;
                } else {
                    //Sleep for an exponentially increasing amount of seconds
                    usleep(pow(2, $i) * 1000);
                }
            }
        }

        //This should never happen.
        throw new \Exception('How\'d you get here?');
    }
}
