<?php

namespace YouzanApiUserBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use YouzanApiUserBundle\Entity\Staff;
use YouzanApiUserBundle\Entity\User;

class StaffTest extends TestCase
{
    private Staff $staff;

    protected function setUp(): void
    {
        $this->staff = new Staff();
    }

    public function testGettersAndSetters(): void
    {
        // Test User
        $user = new User();
        $this->staff->setUser($user);
        $this->assertSame($user, $this->staff->getUser());

        // Test corpName
        $corpName = '有赞科技有限公司';
        $this->staff->setCorpName($corpName);
        $this->assertEquals($corpName, $this->staff->getCorpName());

        // Test kdtId
        $kdtId = 12345678;
        $this->staff->setKdtId($kdtId);
        $this->assertEquals($kdtId, $this->staff->getKdtId());

        // Test corpId
        $corpId = 'CORP123456';
        $this->staff->setCorpId($corpId);
        $this->assertEquals($corpId, $this->staff->getCorpId());

        // Test email
        $email = 'test@youzan.com';
        $this->staff->setEmail($email);
        $this->assertEquals($email, $this->staff->getEmail());

        // Test name
        $name = '张三';
        $this->staff->setName($name);
        $this->assertEquals($name, $this->staff->getName());

        // Test null values
        $this->staff->setCorpName(null);
        $this->assertNull($this->staff->getCorpName());
        
        $this->staff->setKdtId(null);
        $this->assertNull($this->staff->getKdtId());
        
        $this->staff->setCorpId(null);
        $this->assertNull($this->staff->getCorpId());
        
        $this->staff->setEmail(null);
        $this->assertNull($this->staff->getEmail());
        
        $this->staff->setName(null);
        $this->assertNull($this->staff->getName());
    }

    public function testToString(): void
    {
        $user = new User();
        $this->staff->setUser($user);
        
        // 当 ID 为 0 时（默认值），应该返回 '[]'
        $this->assertEquals('[]', (string)$this->staff);

        $this->staff->setName('李四');
        $this->staff->setEmail('lisi@youzan.com');
        $this->assertEquals('李四[lisi@youzan.com]', (string)$this->staff);
    }
}