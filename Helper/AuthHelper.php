<?php

namespace MauticPlugin\MauticLassoBundle\Helper;

use Mautic\Auth\ApiAuth;
use Mautic\CoreBundle\Helper\CoreParametersHelper;

class AuthHelper
{
    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    public function __construct(
        CoreParametersHelper $coreParametersHelper
    ) {
        $this->coreParametersHelper = $coreParametersHelper;
    }

    public function basicAuth()
    {
        
        $username = $apiKey = $this->coreParametersHelper->get('username');
        $password = $apiKey = $this->coreParametersHelper->get('userpass');
        // ApiAuth->newAuth() will accept an array of Auth settings
        $settings = [
            'userName'   => $username,             // username of a user      
            'password'   => $password,             // Make it a secure password
        ];
        
        // Initiate the auth object specifying to use BasicAuth
        $initAuth = new ApiAuth();
        $auth     = $initAuth->newAuth($settings, 'BasicAuth');
        return $auth;

    }
}