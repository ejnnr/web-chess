<?php namespace App\Services;

use Exception;
use Config;
use App\Entities\User;
use App\Entities\Game;
use App\Entities\Tag;

class ResourceUrl
{
    /**
     * Generate the Url to a given resource
     *
     * @param mixed $resource
     * @return string
     */
    public function generate($resource)
    {
        if ($resource instanceof User) {
            $path = 'users';
        } elseif ($resource instanceof Game) {
            $path = 'games';
        } elseif ($resource instanceof Tag) {
            $path = 'tags';
        } else {
            throw new Exception('Resource type not recognized');
        }
        
        return $this->baseApiUrl() . '/' . $path . '/' . $resource->id;
    }

    /**
     * The base URL of the entire application
     *
     * @return string
     */
    public function baseUrl()
    {
        return Config::get('app.url');
    }

    /**
     * The base URL of the REST API
     *
     * @return string
     */
    public function baseApiUrl()
    {
        return $this->baseUrl() . '/api';
    }
}
