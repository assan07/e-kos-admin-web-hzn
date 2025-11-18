<?php

namespace App\Services;

use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Kreait\Firebase\Exception\AuthException;
use Exception;

class FirebaseAuthService
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function login($email, $password)
    {
        try {
            return $this->auth->signInWithEmailAndPassword($email, $password);

        } catch (InvalidPassword $e) {
            throw new Exception("Wrong password");

        } catch (UserNotFound $e) {
            throw new Exception("User not found");

        } catch (AuthException $e) {
            throw new Exception("Firebase auth error: ".$e->getMessage());

        } catch (\Throwable $e) {
            throw new Exception("Unexpected auth error: ".$e->getMessage());
        }
    }
}
