<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Auth;
use Core\Controller;

/**
 * Handles authentication - login and logout.
 */
class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/admin');
            return;
        }

        $this->render('auth/login', [
            'title' => 'Log in',
            'error' => null,
        ], 'auth');
    }

    /**
     * Process login attempt.
     */
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('/admin');
            return;
        }

        if (!$this->validateCsrf()) {
            $this->render('auth/login', [
                'title' => 'Log in',
                'error' => 'Your session has expired. Please try again.',
            ], 'auth');
            return;
        }

        // Check if login is blocked due to too many attempts
        $blockTime = Auth::getBlockTimeRemaining();
        if ($blockTime !== null) {
            $minutes = ceil($blockTime / 60);
            $this->render('auth/login', [
                'title' => 'Log in',
                'error' => "Too many failed login attempts. Please try again in {$minutes} minute(s).",
            ], 'auth');
            return;
        }

        $password = $this->post('password', '');

        if (Auth::attempt($password)) {
            $this->redirect('/admin');
            return;
        }

        $attempts = Auth::getFailedAttempts();
        $remaining = 5 - $attempts;

        if ($remaining > 0) {
            $error = "The password is not correct. {$remaining} attempt(s) remaining.";
        } else {
            $error = 'Too many failed login attempts. Your account is temporarily locked.';
        }

        $this->render('auth/login', [
            'title' => 'Log in',
            'error' => $error,
        ], 'auth');
    }

    /**
     * Log out and redirect to login.
     */
    public function logout(): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid logout request. Please try again.');
            $this->redirect('/admin');
            return;
        }

        Auth::logout();
        $this->redirect('/');
    }
}
