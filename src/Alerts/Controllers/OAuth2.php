<?php namespace Alerts\Controllers;

use \Symfony\Component\HttpFoundation;
use \OAuth2\HttpFoundationBridge;
use \Alerts\Repositories\Interfaces;

class OAuth2
{
    /**
     * @var \OAuth2\Server
     */
    private $server;

    /**
     * @var Interfaces\Users
     */
    private $usersRepo;

    /**
     * @var \Monolog\Logger
     */
    private $log;

    public function __construct(\OAuth2\Server $server, Interfaces\Users $usersRepo, \Monolog\Logger $log)
    {
        $this->server = $server;
        $this->usersRepo = $usersRepo;
        $this->log = $log;
    }

    /**
     * Validates a request and takes a scope value that could result
     * in a user id being put into the request if it's valid.
     *
     * @param HttpFoundation\Request $request
     * @param string $scope
     * @return null|HttpFoundation\Response
     */
    public function validateRequest(HttpFoundation\Request $request, $scope)
    {
        $this->log->addDebug(print_r($request, true), [
            'namespace' => 'Alerts\\Controllers\\OAuth2',
            'method' => 'validateRequest',
            'type' => 'request',
            'scope' => $scope
        ]);

        $bridgeRequest = HttpFoundationBridge\Request::createFromRequest($request);

        if ($this->server->verifyResourceRequest($bridgeRequest, null, $scope)) {
            //Put the user into the request if we're validating at the user scope
            if ($scope === 'user') {
                $token = $this->server->getAccessTokenData($bridgeRequest);
                $request->request->set('user', $this->usersRepo->getById($token['user_id']));
            } else {
                //Set the user to null which should make any
                //searches relying on this being valid to fail.
                $request->request->set('user', null);
            }

            return null;
        }

        $this->log->addWarning('Failed to validate request', [
            'namespace' => 'Alerts\\Controllers\\OAuth2',
            'method' => 'validateRequest',
            'scope' => $scope
        ]);

        return new HttpFoundation\Response('Not Authorized', 401);
    }
}
