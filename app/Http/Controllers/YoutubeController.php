<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_YouTube;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\File;

class YoutubeController extends Controller
{
    private $clientId;
    private $clientSecret;

    protected $googleClient;
    protected $youtubeClient;

    private $users = [
        'vicky'      => 'UCbtWi24JfohnP-sNaVGv-LQ',
        'outromaker' => 'UCXDYn8ne4p5jNWu0xAs0Ctw',
    ];

    public function check($username = null)
    {
        if (is_null($username)) {
            return "Provide a user youtube name.";
        }

        if (!array_key_exists($username, $this->users)) {
            return "User does not exist.";
        }

        $channelId = $this->users[$username];

        $this->clientId     = getenv('GOOGLE_CLIENT_ID');
        $this->clientSecret = getenv('GOOGLE_CLIENT_SECRET');

        $this->youtubeClient = $this->connect();

        $channel = $this->youtubeClient->channels->listChannels('statistics', ['id' => $channelId]);

        $subs = $channel->getItems()[0]->getStatistics()->getSubscriberCount();

        return view('subs', compact('subs'));
    }

    public function number()
    {
        $this->clientId     = getenv('GOOGLE_CLIENT_ID');
        $this->clientSecret = getenv('GOOGLE_CLIENT_SECRET');

        $this->youtubeClient = $this->connect();

        $channel = $this->youtubeClient->channels->listChannels('statistics', ['id' => 'UCbtWi24JfohnP-sNaVGv-LQ']);

        return (int) $channel->getItems()[0]->getStatistics()->getSubscriberCount();
    }

    public function connect()
    {
        $this->googleClient = new Google_Client();

        $this->googleClient->setClientId($this->clientId);
        $this->googleClient->setClientSecret($this->clientSecret);
        $this->googleClient->setAccessType('offline');
        $this->setAccesstoken();


        return new Google_Service_YouTube($this->googleClient);
    }


    private function setAccesstoken()
    {
        $accessToken = File::get(public_path('key.txt'));

        $this->googleClient->setAccessToken($accessToken);

        if ($this->googleClient->isAccessTokenExpired()) {
            $this->refreshToken();
        }
    }


    private function refreshToken()
    {
        $newToken = json_decode($this->googleClient->getAccessToken());
        $this->googleClient->refreshToken($newToken->refresh_token);

        File::put(public_path('key.txt'), $this->googleClient->getAccessToken());
    }
}
