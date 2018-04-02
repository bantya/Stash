<?php

use PHPUnit\Framework\TestCase;
use PHLAK\Stash;

class MemcachedTest extends TestCase
{
    use Cacheable;

    protected $stash;

    public function setUp()
    {
        $this->stash = new Stash\Drivers\Memcached(function ($memcached) {
            $memcached->addServer('localhost', 11211);
        });
    }

    public function test_it_returns_false_for_an_expired_item()
    {
        $this->stash->put('expired', 'qwerty', -5);

        $this->assertFalse($this->stash->get('expired'));
    }
}
