<?php

namespace MyFonero\Controllers;

class AuthController extends BaseController {

    protected $settings;

    public function __construct($c)
    {
        parent::__construct($c);

        $this->settings = $this->container->get('settings');
    }

    public function registration($request, $response, $args) {
	$wallet = md5($this->settings['salt'] . $args['login'] . $request->getParsedBody()['password']);
	if (file_exists($this->settings['webWalletsDir'] . $wallet)) {
		return $response->withJson([
            		'status' => 'error',
	        ]);
	}

	$password = md5($this->settings['salt'] . $request->getParsedBody()['password']);

	$output = `{$this->settings['rpc']} '{"jsonrpc":"2.0","id":"$wallet","method":"create_wallet","params":{"filename":"$wallet","password":"$password","language":"English"}}' -H 'Content-Type: application/json'`;
	$output = `{$this->settings['rpc']} '{"jsonrpc":"2.0","id":"$wallet","method":"open_wallet","params":{"filename":"$wallet","password":"$password"}}' -H 'Content-Type: application/json'`;
	$output = `{$this->settings['rpc']} '{"jsonrpc":"2.0","id":"$wallet","method":"getaddress"}' -H 'Content-Type: application/json'`;
	$address = json_decode($output)->result->address;

	$output = `{$this->settings['rpc']} '{"jsonrpc":"2.0","id":"$wallet","method":"getbalance"}' -H 'Content-Type: application/json'`;
	$balance = json_decode($output)->result->balance;
	$unlocked_balance = json_decode($output)->result->unlocked_balance;

        return $response->withJson([
            'status' => 'ok',
	    'address' => $address,
	    'balance' => $balance,
	    'unlocked_balance' => $unlocked_balance
        ]);
    }

    public function login($request, $response, $args) {
	$wallet = md5($this->settings['salt'] . $args['login'] . $request->getParsedBody()['password']);

	if (!file_exists($this->settings['webWalletsDir'] . $wallet)) {
	    return $response->withJson([
              'status' => 'error',
              'address' => '',
            ]);
	}

	$password = md5($this->settings['salt'] . $request->getParsedBody()['password']);

	$output = `{$this->settings['rpc']} '{"jsonrpc":"2.0","id":"$wallet","method":"open_wallet","params":{"filename":"$wallet","password":"$password"}}' -H 'Content-Type: application/json'`;
        $output = `{$this->settings['rpc']} '{"jsonrpc":"2.0","id":"$wallet","method":"getaddress"}' -H 'Content-Type: application/json'`;
        $address = json_decode($output)->result->address;

	$output = `{$this->settings['rpc']} '{"jsonrpc":"2.0","id":"$wallet","method":"rescan_blockchain"}' -H 'Content-Type: application/json'`;
	$output = `{$this->settings['rpc']} '{"jsonrpc":"2.0","id":"$wallet","method":"rescan_spent"}' -H 'Content-Type: application/json'`;

        $output = `{$this->settings['rpc']} '{"jsonrpc":"2.0","id":"$wallet","method":"getbalance"}' -H 'Content-Type: application/json'`;
        $balance = json_decode($output)->result->balance;
        $unlocked_balance = json_decode($output)->result->unlocked_balance;

        return $response->withJson([
            'status' => 'ok',
	    'address' => $address,
	    'balance' => $balance/1000000000000,
            'unlocked_balance' => $unlocked_balance/1000000000000
        ]);
    }

    public function send($request, $response, $args) {
        $wallet = md5($this->settings['salt'] . $args['login'] . $request->getParsedBody()['password']);

        if (!file_exists($this->settings['webWalletsDir'] . $wallet)) {
            return $response->withJson([
              'status' => 'error',
              'address' => '',
            ]);
        }

	$password = md5($this->settings['salt'] . $request->getParsedBody()['password']);

        $output = `{$this->settings['rpc']} '{"jsonrpc":"2.0","id":"$wallet","method":"open_wallet","params":{"filename":"$wallet","password":"$password"}}' -H 'Content-Type: application/json'`;

        $output = `{$this->settings['rpc']} '{"jsonrpc":"2.0","id":"$wallet","method":"getbalance"}' -H 'Content-Type: application/json'`;
        $balance = json_decode($output)->result->balance;
        $unlocked_balance = json_decode($output)->result->unlocked_balance;

	$recipient = preg_replace("/[^a-zA-Z0-9]/","",$request->getParsedBody()['recipient']);
	$amount = (int)($request->getParsedBody()['amount']*1000000000000);

	if ($amount > $unlocked_balance) {
		return $response->withJson([
            		'status' => 'error',
        	]);
	}

	$output = `{$this->settings['rpc']} '{"jsonrpc":"2.0","id":"$wallet","method":"transfer","params":{"destinations":[{"amount":$amount,"address":"$recipient"}],"mixin":4,"get_tx_key": true}}' -H 'Content-Type: application/json'`;
	$result = json_decode($output)->result;

	if (!$result->fee || !$result->tx_hash || !$result->tx_key) {
	 	return $response->withJson([
                        'status' => 'error',
                ]);
	}

	return $response->withJson([
            'status' => 'ok',
	    'fee' => $result->fee,
	    'tx_hash' => $result->tx_hash,
	    'tx_key' => $result->tx_key
        ]);
    }

}
