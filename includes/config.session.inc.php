<?php
class SessionManager
{
    private int $lifetime;
    private string $domain;
    private string $path;
    private bool $secure;
    private bool $httponly;
    private int $regenerationInterval;

    public function __construct(
        int $lifetime = 1800,
        string $domain = 'localhost',
        string $path = '/',
        bool $secure = true,
        bool $httponly = true,
        int $regenerationInterval = 1800
    ) {
        $this->lifetime = $lifetime;
        $this->domain = $domain;
        $this->path = $path;
        $this->secure = $secure;
        $this->httponly = $httponly;
        $this->regenerationInterval = $regenerationInterval;

        $this->configureSession();
        $this->startSession();
        $this->checkRegeneration();
    }

    private function configureSession(): void
    {
        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_strict_mode', 1);

        session_set_cookie_params([
            'lifetime' => $this->lifetime,
            'domain' => $this->domain,
            'path' => $this->path,
            'secure' => $this->secure,
            'httponly' => $this->httponly
        ]);
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function checkRegeneration(): void
    {
        if (!isset($_SESSION['last_regeneration'])) {
            $this->regenerateSessionId();
        } elseif (time() - $_SESSION['last_regeneration'] >= $this->regenerationInterval) {
            $this->regenerateSessionId();
        }
    }

    public function regenerateSessionId(): void
    {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

$session = new SessionManager();
?>