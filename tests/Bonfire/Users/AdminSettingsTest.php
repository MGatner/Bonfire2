<?php

namespace Tests\Bonfire\Users;

use App\Entities\User;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class AdminSettingsTest extends TestCase
{
    protected $refresh = true;

    /**
     * @var User
     */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
    }

    public function testCanSeePage()
    {
        $result = $this->actingAs($this->user)
            ->get('/admin/settings/users');

        $result->assertStatus(200);
        $result->assertSee('Registration');
    }

    public function testSubmitForm()
    {
        $result = $this->actingAs($this->user)
            ->post('/admin/settings/users', [
                'allowRegistration'     => 1,
                'emailActivation'       => 1,
                'allowRemember'         => 'on',
                'rememberLength'        => 55,
                'email2FA'              => 1,
                'minimumPasswordLength' => 10,
                'validators'            => [
                    'Sparks\Shield\Authentication\Passwords\CompositionValidator',
                ],
                'defaultGroup' => 'developer',
            ]);

        $result->assertRedirect();

        $this->assertTrue(setting('Auth.allowRegistration'));
        $this->assertSame(10, setting('Auth.minimumPasswordLength'));
        $this->assertSame('developer', setting('AuthGroups.defaultGroup'));
        $this->assertSame(['Sparks\Shield\Authentication\Passwords\CompositionValidator'], setting('Auth.passwordValidators'));
        $this->assertSame(['login' => '1', 'register' => '1'], setting('Auth.actions'));
    }
}
