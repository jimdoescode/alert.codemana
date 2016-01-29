<?php namespace Alerts\Repositories\Http;

use \Alerts\Repositories\Interfaces;
use \Alerts\Models;
use Symfony\Component\HttpFoundation;

class GitHub implements Interfaces\GitHub
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    public function __construct($clientId, $clientSecret, $oauthToken = null)
    {
        $headers = [];
        $headers['Accept'] = 'application/json';
        if (!is_null($oauthToken)) {
            $headers['Authorization'] = "token {$oauthToken}";
        }

        $this->client = new \GuzzleHttp\Client([
            // Default parameters
            'defaults' => ['debug' => false, 'exceptions' => false],
            // Base URI is used with relative requests
            'base_uri' => 'https://api.github.com',
            // You can set any number of default request options.
            'timeout'  => 2.0,
            'headers' => $headers
        ]);

        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Calls the route `/repos/:owner/:repo/compare/:base...:head` and returns all the patches
     * with a status that matches the specified status filter.
     *
     * @param string $repo
     * @param string $base
     * @param string $head
     * @param array $fileEditors
     * @param array $statusFilter
     * @return Models\PatchFile[]
     */
    public function getChangePatches($repo, $base, $head, $fileEditors, $statusFilter = [])
    {
        $response = $this->retryWithExponentialBackoff(3, function () use ($repo, $base, $head) {

            return $this->client->get("/repos/{$repo}/compare/{$base}...{$head}");

        });

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $comparison = json_decode($response->getBody(), true);

            $filtered =  array_filter($comparison['files'], function ($file) use ($statusFilter) {
                return in_array($file['status'], $statusFilter);
            });

            $patchModels = [];
            foreach ($filtered as $file) {
                $editors = isset($fileEditors[$file['filename']]) ? $fileEditors[$file['filename']] : [];
                $patchModels[] = $this->patchToModel($file['filename'], $file['patch'], $editors);
            }
            return $patchModels;
        }
        return [];
    }

    private function patchToModel($filename, $rawFile, $fileEditors)
    {
        $patch = new Models\PatchFile();
        $patch->chunks = [];
        $patch->name = $filename;
        $patch->raw = $rawFile;
        $patch->editors = $fileEditors;

        if (preg_match_all('/@@ \-(\d+),\d+ \+(\d+),\d+ @@/', $rawFile, $matches, PREG_OFFSET_CAPTURE)) {

            $count = count($matches[0]);
            for ($i = 0; $i < $count; $i++) {
                $chunk = new Models\PatchChunk();
                $chunk->header = $matches[0][$i][0];
                $rawChunk = $i < ($count - 1) ?
                    substr($rawFile, $matches[0][$i][1], $matches[0][$i+1][1] - $matches[0][$i][1]) :
                    substr($rawFile, $matches[0][$i][1]);

                $negLineNumber = $matches[1][$i][0];
                $posLineNumber = $matches[2][$i][0];
                $lines = explode("\n", $rawChunk);
                array_shift($lines);
                foreach ($lines as $line) {
                    //Diff outputs this extra line if there is no newline at
                    //the end of a file so we need to detect it and remove it
                    if ($line === 'No newline at end of file') {
                        break;
                    }

                    $parsedLine = new Models\PatchLine();
                    $parsedLine->raw = $line;
                    $parsedLine->isAdded = ($line[0] === '+');
                    $parsedLine->isRemoved = ($line[0] === '-');
                    $parsedLine->parsed = htmlspecialchars(substr($line, 1));
                    if ($parsedLine->isAdded) {
                        $parsedLine->newNumber = $posLineNumber;
                        $posLineNumber++;
                    } elseif ($parsedLine->isRemoved) {
                        $parsedLine->oldNumber = $negLineNumber;
                        $negLineNumber++;
                    } else {
                        $parsedLine->newNumber = $posLineNumber;
                        $parsedLine->oldNumber = $negLineNumber;
                        $posLineNumber++;
                        $negLineNumber++;
                    }
                    $chunk->lines[] = $parsedLine;
                }
                $patch->chunks[] = $chunk;
            }
        }

        return $patch;
    }

    public function getUserFromOAuth($code)
    {
        $response = $this->retryWithExponentialBackoff(3, function () use ($code) {
            return $this->client->post('/login/oauth/access_token', [
                'base_uri' => 'https://github.com',
                'body' => "client_id={$this->clientId}&client_secret={$this->clientSecret}&code={$code}"
            ]);
        });

        $user = new Models\User();
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $authorization = json_decode($response->getBody(), true);
            if (array_key_exists('error', $authorization)) {
                //TODO: Add a logger and log this
                throw new \Exception($authorization['error_description']);
            }
            $user->githubAccessToken = $authorization['access_token'];
        }

        //Fetch user data.
        $response = $this->retryWithExponentialBackoff(3, function () use ($user) {
            return $this->client->get('/user', [
                'headers' => [
                    'Authorization' => "token {$user->githubAccessToken}",
                ]
            ]);
        });

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $user->email = $response['email'];
            $user->githubId = $response['id'];
        }

        //Fetch user's repos.
        $response = $this->retryWithExponentialBackoff(3, function () use ($user) {
            return $this->client->get('/user/repos', [
                'headers' => [
                    'Authorization' => "token {$user->githubAccessToken}",
                ]
            ]);
        });

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $repos = json_decode($response->getBody(), true);
            $user->githubRepos = [];
            foreach ($repos as $repo) {
                $model = new Models\Repo();
                $model->name = $repo['full_name'];
                $model->isAdmin = $repo['permissions']['admin'];
                $user->githubRepos[] = $model;
            }
        }

        return isset($user->githubId, $user->githubAccessToken) ? $user : null;
    }

    public function getAuthorizationRedirect()
    {
        $url = "https://github.com/login/oauth/authorize?client_id={$this->clientId}&scope=user:email,repo";
        return new HttpFoundation\RedirectResponse($url);
    }

    public function installHook(Models\User $user, Models\WatchedRepo $repo, $callbackUrl)
    {
        $response = $this->retryWithExponentialBackoff(3, function () use ($user, $repo, $callbackUrl) {

            $request = [
                'name' => 'web',
                'active' => true,
                'config' => [
                    'url' => "{$callbackUrl}/hooks/github",
                    'content_type' => 'json',
                    'secret' => $repo->secret,
                ]
            ];

            return $this->client->post("/repos/{$repo->name}/hooks", [
                'headers' => ['Authorization' => "token {$user->githubAccessToken}"],
                'body' => json_encode($request)
            ]);
        });

        //If we were successful return a watched repo model otherwise return null.
        //TODO: If we weren't successful then we should probably log what the response is.
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
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
