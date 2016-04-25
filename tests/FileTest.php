<?php

class FileTest extends PHPUnit_Framework_TestCase {

    protected $stash;

    public function setUp() {
        $this->stash = new Stash\Drivers\File(__DIR__ . '/cache');
    }

    public function tearDown() {
        $this->stash->flush();
    }

    /** @test */
    public function it_can_add_and_retrieve_an_item() {
        $this->assertTrue($this->stash->put('put', 'jabberwocky', 5));
        $this->assertEquals('jabberwocky', $this->stash->get('put'));
    }

    /** @test */
    public function it_can_add_and_retrieve_an_item_forever() {
        $this->assertTrue($this->stash->forever('diamonds', ['Hope', 'Pink Panther', 'Tiffany']));
        $this->assertEquals(['Hope', 'Pink Panther', 'Tiffany'], $this->stash->get('diamonds'));
    }

    /** @test */
    public function it_returns_false_for_nonexistant_items() {
        $this->assertFalse($this->stash->get('nonexistant-item'));
    }

    /** @test */
    public function it_returns_a_default_value() {
        $this->assertNull($this->stash->get('nonexistant-item', null));
    }

    /** @test */
    public function it_returns_true_if_it_has_an_item() {
        $this->stash->put('has', 'some-item', 5);
        $this->assertTrue($this->stash->has('has'));
    }

    /** @test */
    public function it_returns_true_if_it_has_a_boolean_false() {
        $this->stash->put('false', false, 5);
        $this->assertTrue($this->stash->has('false'));
    }

    /** @test */
    public function it_returns_false_if_it_doesnt_have_an_item() {
        $this->assertFalse($this->stash->has('nonexistant-item'));
    }

    /** @test */
    public function it_remembers_an_already_cached_item() {

        $this->stash->put('remember-pre-existing', "Don't override me bro!", 5);

        $text = $this->stash->remember('remember-pre-existing', 5, function() {
            return "I wont't override him.";
        });

        $this->assertEquals("Don't override me bro!", $text);

    }

    /** @test */
    public function it_remembers_an_already_cached_boolean_false() {

        $this->stash->put('boolean', false);

        $boolean = $this->stash->remember('boolean', 5, function() {
            return 'Not boolean false';
        });

        $this->assertFalse($boolean);

    }

    /** @test */
    public function it_remembers_a_nonexistant_item() {

        $date = $this->stash->remember('remember-new-item', 5, function() {
            return 'Pork Chops';
        });

        $this->assertEquals('Pork Chops', $date);

    }

    /** @test */
    public function it_remembers_an_already_cached_item_forever() {

        $this->stash->put('remember-forever-pre-existing', 'I already exist', 5);

        $text = $this->stash->rememberForever('remember-forever-pre-existing', function() {
            return "I don't yet exist.";
        });

        $this->assertEquals('I already exist', $text);

    }

    /** @test */
    public function it_remembers_a_nonexistant_item_forever() {

        $date = $this->stash->rememberForever('remember-remember', function() {
            return 'November 5th';
        });

        $this->assertEquals('November 5th', $date);

    }

    /** @test */
    public function it_can_increment_an_item() {
        $this->stash->put('inc', 1336);
        $this->assertEquals(1337, $this->stash->increment('inc'));
    }

    /** @test */
    public function it_can_increment_an_item_by_a_custom_ammount() {
        $this->stash->put('inc-custom', 1327);
        $this->assertEquals(1337, $this->stash->increment('inc-custom', 10));
    }

    /** @test */
    public function it_can_decrement_an_item() {
        $this->stash->put('dec', 1338);
        $this->assertEquals(1337, $this->stash->decrement('dec'));
    }

    /** @test */
    public function it_can_decrement_an_item_by_a_custom_ammount() {
        $this->stash->put('dec-custom', 1347);
        $this->assertEquals(1337, $this->stash->decrement('dec-custom', 10));
    }

    /** @test */
    public function it_returns_false_when_incrementing_a_non_integer() {
        $this->stash->put('non-integer', 'potato');
        $this->assertFalse($this->stash->increment('non-integer'));
    }

    /** @test */
    public function it_returns_false_when_incrementing_a_nonexistant_item() {
        $this->assertFalse($this->stash->increment('nonexistant-item'));
    }

    /** @test */
    public function it_can_forget_an_item() {
        $this->stash->put('forgettable', 'asdf', 5);
        $this->assertTrue($this->stash->forget('forgettable'));
        $this->assertFalse($this->stash->has('forgettable'));
        $this->assertFalse($this->stash->get('forgettable'));
    }

    /** @test */
    public function it_is_flushable() {
        $this->assertTrue($this->stash->flush());
    }

}
