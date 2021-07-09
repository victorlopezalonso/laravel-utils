<?php

namespace Victorlopezalonso\App\Providers;

use Laravel\Socialite\Two\GoogleProvider;

class GoogleCustomProvider extends GoogleProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://www.googleapis.com/oauth2/v3/tokeninfo', [
            'query' => [
                'prettyPrint' => 'false',
                'id_token' => $token,
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
