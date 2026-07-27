<?php

namespace Tests;

use Illuminate\Contracts\Validation\UncompromisedVerifier;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Passwords use `->uncompromised()`, which calls the HaveIBeenPwned API.
        // Bind a stub so the suite never hits the network; tests that need to
        // exercise the check rebind this to a verifier that reports a breach.
        $this->app->bind(UncompromisedVerifier::class, fn (): UncompromisedVerifier => new class implements UncompromisedVerifier
        {
            public function verify($data): bool
            {
                return true;
            }
        });
    }
}
