<?php

/**
 * This is an example of how to use the InternalClientTransportProvider
 *
 * For more information go to:
 * http://voryx.net/creating-internal-client-thruway/
 */
class InternalClient extends Thruway\Peer\Client
{

    public function onSessionStart($session, $transport)
    {
        $this->getCallee()->register($this->session, 'com.example.ping', array($this, 'callPing'));

        $this->getCallee()->register($this->session, 'com.example.publish', array($this, 'callPublish'));

    }

    function start()
    {
    }

    function callPing($res)
    {
        return array($res[0]);
    }

    function callPublish($args)
    {
        $deferred = new \React\Promise\Deferred();

        $this->getPublisher()->publish($this->session, "com.example.publish", [$args[0]], ["key1" => "test1", "key2" => "test2"], ["acknowledge" => true])
            ->then(
                function () use ($deferred) {
                    $deferred->resolve('ok');
                },
                function ($error) use ($deferred) {
                    $deferred->reject("failed: {$error}");
                }
            );

        return $deferred->promise();
    }


}