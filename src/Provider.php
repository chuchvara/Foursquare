<?php

namespace SocialiteProviders\Foursquare;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://foursquare.com/oauth2/authenticate', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://foursquare.com/oauth2/access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://api.foursquare.com/v2/users/self?oauth_token='.$token.'&v=20150214'
        );

        return json_decode($response->getBody()->getContents(), true)['response']['user'];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
    	$lastName = isset($user['lastName']) ? $user['lastName'] : '';
    	
        return (new User())->setRaw($user)->map([
            'id' => $user['id'], 'nickname' => null,
            'name' => $user['firstName'].' '.$lastName,
            'email' => $user['contact']['email'],
            'avatar' => $user['photo']['prefix'].$user['photo']['suffix'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
