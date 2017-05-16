<?php

namespace Mopinion\Api;

use Guzzle\Http\Client as HttpClient;

class BaseClient
{
    const API_VERSION   = '1.0.0-beta';
    const API_URL       = 'https://apimopinion.com';

    public function __construct() {}
}