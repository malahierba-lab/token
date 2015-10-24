<?php
namespace Malahierba\Token;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use Exception;

class Token
{
    /**
     * The Model whish related a token
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The type for the token
     *
     * @var string
     */
    protected $type;

    /**
     * Minutes for to expire a new token
     *
     * @var string
     */
    protected $expire_in = 60;

    /**
     * Token Length
     *
     * @var string
     */
    protected $token_length = 48;

    /**
     * Create a new Token instance.
     *
     * @param   Illuminate\Database\Eloquent\Model
     * @param   string
     * @param   integer     $expire_in      minutes for to expire a new token
     * @param   integer     $token_length   number of chars for token
     * @return  void
     */
    public function __construct($model, $type, $expire_in = null, $token_length = null)
    {
        $this->model            = $model;
        $this->type             = $type;

        if ($expire_in)
            $this->expire_in    = $expire_in;

        if ($token_length)
            $this->token_length = $token_length;
    }

    /**
     * Get the Token
     *
     * @param   void
     * @return  string
     */
    public function get()
    {
        if (Cache::has($this->getCacheKey()))
            return Cache::get($this->getCacheKey());

        return $this->create();
    }

    /**
     * Check the token
     *
     * @param   string  $token
     * @return  boolean
     */
    public function check($token)
    {
        if (! Cache::has($this->getCacheKey()))
            return false;

        return Cache::get($this->getCacheKey()) === $token;
    }

    /**
     * Delete the token
     *
     * @param   void
     * @return  void
     */
    public function delete()
    {
        if (Cache::has($this->getCacheKey()))
            Cache::forget($this->getCacheKey());
    }

    /**
     * Get the cache key for save the token
     *
     * @param   void
     * @return  string
     */
    protected function getCacheKey()
    {
        $model      = new \ReflectionClass($this->model);

        $namespace  = Str::slug($model->getNamespaceName());
        $class      = Str::slug($model->getShortName());
        $type       = Str::slug($this->type);
        $key        = $this->model->getKey();

        if (empty($key))
            throw new Exception("[Token] Error: Empty value for " . $model->getShortName() . "'s attribute '" . $this->model->getKeyName() . "'.", 1);

        return 'malahierba-token' . '---' . $namespace . '---' . $class . '---' . $type . '---' . $key;
    }

    /**
     * Generate a String for Token
     *
     * @param   void
     * @return  string
     */
    protected function generateTokenString()
    {
        return Str::random($this->token_length);
    }

    /**
     * Create a new token and save as cache var
     *
     * @param   void
     * @return  string
     */
    protected function create()
    {
        $token = $this->generateTokenString();

        Cache::put($this->getCacheKey(), $token, $this->expire_in);

        return $token;
    }

}