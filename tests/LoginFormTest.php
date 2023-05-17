<?php

class LoginFormTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->loginForm = $this
            ->getMockBuilder(\App\Security\LoginFormAuthenticator::class)
            ->getMock();
    }

    function testRequest(){

    }
}