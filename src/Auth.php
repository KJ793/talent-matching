<?php
namespace App;

class Auth
{
    /**
     * Attempt login. Returns true on success and stores user_id / role in session.
     */
    public static function attempt(string $email, string $password): bool
    {
        $user = UserRepository::findByEmail($email);
        if (!$user) {
            return false;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        Session::set('user_id', (int)$user['id']);
        Session::set('role',    $user['role']);
        return true;
    }

    public static function register(string $email, string $password, string $role): ?int
    {
        // Reject duplicate emails up-front.
        if (UserRepository::findByEmail($email)) {
            return null;
        }
        if (!in_array($role, ['candidate', 'employer'], true)) {
            return null;
        }
        return UserRepository::create($email, $password, $role);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    /**
     * Redirect to login if not authenticated.
     * If a role is given, also enforce that the user has that role.
     */
    public static function requireLogin(?string $role = null): void
    {
        if (!Session::isLoggedIn()) {
            header('Location: ' . self::projectRootUrl() . 'login.php');
            exit;
        }
        if ($role !== null && Session::role() !== $role) {
            http_response_code(403);
            echo 'Forbidden — wrong role for this page.';
            exit;
        }
    }

    /**
     * Compute the URL pointing at the project root, regardless of how deep
     * the current request is. This lets the app run from a sub-folder such
     * as XAMPP's htdocs/final_project/ as well as from the web-server root.
     *
     * Strategy: compare $_SERVER['SCRIPT_NAME'] (the URL path to the running
     * script) against the script's filesystem location relative to this
     * file's directory. The number of directories the script is below the
     * project root tells us how many '../' segments to prepend.
     */
    private static function projectRootUrl(): string
    {
        // src/ is one level deep inside the project, so the project root is
        // the parent of __DIR__. SCRIPT_FILENAME is the absolute path of
        // the running script.
        $projectRoot = realpath(__DIR__ . '/..');
        $scriptFile  = realpath($_SERVER['SCRIPT_FILENAME'] ?? '');

        if ($projectRoot && $scriptFile && str_starts_with($scriptFile, $projectRoot)) {
            // How deep is the script inside the project?
            $relative = ltrim(substr($scriptFile, strlen($projectRoot)), DIRECTORY_SEPARATOR);
            $depth = substr_count($relative, DIRECTORY_SEPARATOR);
            if ($depth === 0) {
                return '';  // already at root, plain "login.php" works
            }
            return str_repeat('../', $depth);
        }
        // Fallback: hope the caller is at the root
        return '';
    }
}
