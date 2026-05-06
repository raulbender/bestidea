<?php

declare(strict_types=1);

namespace Tests\Unit;

use Framework\Http\PHPSession;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    public function test_user_can_login_and_check_status(): void
    {
        $session = new PHPSession();
        $session->login(123);

        $this->assertTrue($session->isLoggedIn());
        $this->assertEquals(123, $session->getUserId());
    }


    public function test_logoff_clears_data(): void
    {
        $session = new PHPSession();
        $session->login(123);
        $session->logout();

        $this->assertFalse($session->isLoggedIn());
        $this->assertNull($session->getUserId());
    }


}
