<?php

namespace Sourceboat\Permission\Tests;

class MatchRuleToPermissionTest extends TestCase
{
    public function testDirectRule(): void
    {
        $rule = [
            'user',
            'edit',
        ];

        $this->assertTrue($this->user->matchRuleToPermission($rule, $this->permission));
    }

    public function testSlotRule(): void
    {
        $rule = [
            'user',
            '+',
        ];

        $this->assertTrue($this->user->matchRuleToPermission($rule, $this->permission));
    }

    public function testWildcardRule(): void
    {
        $rule = [
            'user',
            '#',
        ];

        $this->assertTrue($this->user->matchRuleToPermission($rule, $this->permission));
    }

    public function testWildcardAtEndRule(): void
    {
        $rule = [
            'user',
            'edit',
            '#',
        ];

        $this->assertTrue($this->user->matchRuleToPermission($rule, $this->permission));
    }

    public function testIgnoreForbiddenRule(): void
    {
        $rule = [
            '!user',
            'edit',
        ];

        $this->assertTrue($this->user->matchRuleToPermission($rule, $this->permission));
    }

    public function testWrongRule(): void
    {
        $rule = [
            'user',
            'blabla',
        ];

        $this->assertFalse($this->user->matchRuleToPermission($rule, $this->permission));
    }

    public function testMorePreciseRule(): void
    {
        $rule = [
            'user',
            'edit',
            'self',
        ];

        $this->assertFalse($this->user->matchRuleToPermission($rule, $this->permission));
    }

    public function testShortRule(): void
    {
        $rule = [
            'user',
        ];

        $this->assertFalse($this->user->matchRuleToPermission($rule, $this->permission));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->permission = [
            'user',
            'edit',
        ];
        $this->user = User::create(['email' => 'test@user.com']);
    }
}
