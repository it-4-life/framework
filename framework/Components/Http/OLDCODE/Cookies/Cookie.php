<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 * @copyright ©2009-2015
 */
namespace Spiral\Components\Http\Cookies;

class Cookie
{
    /**
     * The name of the cookie.
     *
     * @var string
     */
    private $name = '';

    /**
     * The value of the cookie. This value is stored on the clients computer; do not store sensitive
     * information.
     *
     * @var string|null
     */
    private $value = null;

    /**
     * Cookie lifetime. This value specified in seconds and declares period of time in which cookie
     * will expire relatively to current time() value.
     *
     * @var int|null
     */
    private $lifetime = null;

    /**
     * The path on the server in which the cookie will be available on.
     *
     * If set to '/', the cookie will be available within the entire domain. If set to '/foo/',
     * the cookie will only be available within the /foo/ directory and all sub-directories such as
     * /foo/bar/ of domain. The default value is the current directory that the cookie is being set in.
     *
     * @var string|null
     */
    private $path = null;

    /**
     * The domain that the cookie is available. To make the cookie available on all subdomains of
     * example.com then you'd set it to '.example.com'. The . is not required but makes it compatible
     * with more browsers. Setting it to www.example.com will make the cookie only available in the www
     * subdomain. Refer to tail matching in the spec for details.
     *
     * @var string|null
     */
    private $domain = null;

    /**
     * Indicates that the cookie should only be transmitted over a secure HTTPS connection from the
     * client. When set to true, the cookie will only be set if a secure connection exists.
     * On the server-side, it's on the programmer to send this kind of cookie only on secure connection
     * (e.g. with respect to $_SERVER["HTTPS"]).
     *
     * @var string|null
     */
    private $secure = null;

    /**
     * When true the cookie will be made accessible only through the HTTP protocol. This means that
     * the cookie won't be accessible by scripting languages, such as JavaScript. This setting can
     * effectively help to reduce identity theft through XSS attacks (although it is not supported
     * by all browsers).
     *
     * @var bool
     */
    private $httpOnly = true;

    /**
     * New Cookie instance, cookies used to schedule cookie set while dispatching Response.
     *
     * @link http://php.net/manual/en/function.setcookie.php
     * @param string $name     The name of the cookie.
     * @param string $value    The value of the cookie. This value is stored on the clients computer;
     *                         do not store sensitive information.
     * @param int    $lifetime Cookie lifetime. This value specified in seconds and declares period
     *                         of time in which cookie will expire relatively to current time() value.
     * @param string $path     The path on the server in which the cookie will be available on.
     *                         If set to '/', the cookie will be available within the entire domain.
     *                         If set to '/foo/', the cookie will only be available within the /foo/
     *                         directory and all sub-directories such as /foo/bar/ of domain. The
     *                         default value is the current directory that the cookie is being set in.
     * @param string $domain   The domain that the cookie is available. To make the cookie available
     *                         on all subdomains of example.com then you'd set it to '.example.com'.
     *                         The . is not required but makes it compatible with more browsers.
     *                         Setting it to www.example.com will make the cookie only available in
     *                         the www subdomain. Refer to tail matching in the spec for details.
     * @param bool   $secure   Indicates that the cookie should only be transmitted over a secure HTTPS
     *                         connection from the client. When set to true, the cookie will only be
     *                         set if a secure connection exists. On the server-side, it's on the
     *                         programmer to send this kind of cookie only on secure connection (e.g.
     *                         with respect to $_SERVER["HTTPS"]).
     * @param bool   $httpOnly When true the cookie will be made accessible only through the HTTP
     *                         protocol. This means that the cookie won't be accessible by scripting
     *                         languages, such as JavaScript. This setting can effectively help to
     *                         reduce identity theft through XSS attacks (although it is not supported
     *                         by all browsers).
     */
    public function __construct(
        $name,
        $value = null,
        $lifetime = null,
        $path = null,
        $domain = null,
        $secure = false,
        $httpOnly = true
    )
    {
        $this->name = $name;
        $this->value = $value;
        $this->lifetime = $lifetime;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }

    /**
     * The name of the cookie.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * The value of the cookie. This value is stored on the clients computer; do not store sensitive
     * information.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * The time the cookie expires. This is a Unix timestamp so is in number of seconds since the epoch.
     * In other words, you'll most likely set this with the time function plus the number of seconds
     * before you want it to expire. Or you might use mktime.
     *
     * Will return null if lifetime is not specified.
     *
     * @return int|null
     */
    public function getExpires()
    {
        if (empty($this->lifetime))
        {
            return null;
        }

        return time() + $this->lifetime;
    }

    /**
     * The path on the server in which the cookie will be available on.
     *
     * If set to '/', the cookie will be available within the entire domain. If set to '/foo/',
     * the cookie will only be available within the /foo/ directory and all sub-directories such as
     * /foo/bar/ of domain. The default value is the current directory that the cookie is being set in.
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * The domain that the cookie is available. To make the cookie available on all subdomains of
     * example.com then you'd set it to '.example.com'. The . is not required but makes it compatible
     * with more browsers. Setting it to www.example.com will make the cookie only available in the www
     * subdomain. Refer to tail matching in the spec for details.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Indicates that the cookie should only be transmitted over a secure HTTPS connection from the
     * client. When set to true, the cookie will only be set if a secure connection exists.
     * On the server-side, it's on the programmer to send this kind of cookie only on secure connection
     * (e.g. with respect to $_SERVER["HTTPS"]).
     *
     * @return bool
     */
    public function getSecure()
    {
        return $this->secure;
    }

    /**
     * When true the cookie will be made accessible only through the HTTP protocol. This means that
     * the cookie won't be accessible by scripting languages, such as JavaScript. This setting can
     * effectively help to reduce identity theft through XSS attacks (although it is not supported
     * by all browsers).
     *
     * @return bool
     */
    public function getHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * Get new cookie with altered value. Original cookie object should not be changed.
     *
     * @param string $value
     * @return Cookie
     */
    public function withValue($value)
    {
        $cookie = clone $this;
        $cookie->value = $value;

        return $cookie;
    }

    /**
     * Convert cookie instance to string.
     *
     * @return string
     */
    public function packHeader()
    {
        $header = array(
            rawurlencode($this->name) . '=' . rawurlencode($this->value)
        );

        if ($this->lifetime !== null)
        {
            $header['expires'] = gmdate(\DateTime::COOKIE, $this->getExpires());
            $header[] = 'Max-Age=' . $this->lifetime;
        }

        if (!empty($this->path))
        {
            $header[] = 'path=' . $this->path;
        }

        if (!empty($this->domain))
        {
            $header[] = $this->domain;
        }

        if ($this->secure)
        {
            $header[] = 'secure';
        }

        if ($this->httpOnly)
        {
            $header[] = 'httponly';
        }

        return join('; ', $header);
    }

    /**
     * Get string representation of header.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->packHeader();
    }
}