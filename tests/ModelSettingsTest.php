<?php

use Faker\Factory;
use RolfHaug\ModelSettings\ModelSettings;
use RolfHaug\ModelSettings\Tests\UserSettings;

class ModelSettingsTest extends TestCase
{

    /**
     * Create a new fake user and return the settings
     *
     * @return mixed
     */
    protected function settings() {
        return ($this->createUser())->settings;
    }

    /** @test */
    public function it_sets_value_to_db()
    {
        $user = $this->createUser();
        $user->settings->set('my_setting', 'my_value');

        $setting = UserSettings::where('user_id', $user->id)->whereSetting(strtoupper('my_setting'))->first();
        $this->assertEquals('my_value', $setting->value);
    }

    /** @test */
    public function it_can_set_array_of_settings()
    {
        $settings = [
            'first_setting' => 'first_value',
            'second_setting' => 'second_value'
        ];

        $user = $this->createUser();
        $user->settings->set($settings);

        foreach($settings as $key => $value) {
            $this->assertEquals($user->settings->{$key}, $value);
        }
    }

    /** @test */
    public function it_throws_error_when_value_is_null()
    {
        try {
            $this->settings()->set('my_setting', null);
        }

        catch(\Exception $e){
            $this->assertContains('cannot be NULL', $e->getMessage());
            return;
        }
        $this->fail("Set method didn't throw error on NULL value");
    }

    /** @test */
    public function it_gets_value_from_db_as_settings_property()
    {
        $user = $this->createUser();

        UserSettings::create([
            'user_id' => $user->id,
            'setting' => 'awesome_setting',
            'value' => 'awesome_value'
        ]);

        $this->assertEquals($user->settings->awesome_setting, 'awesome_value');
    }

    /** @test */
    public function it_gets_null_if_setting_does_not_exists()
    {
        $this->assertNull($this->settings()->setting_that_does_not_exists);
    }

    /** @test */
    public function it_can_get_all_settings()
    {
        $settings = $this->settings();

        $settings->set([
            'first_setting' => 'value',
            'second_setting' => 1,
            'third_setting' => true
        ]);

        $settings = $settings->all();
        $this->assertTrue(is_array($settings));
        $this->assertArrayHasKey('first_setting', $settings);
        $this->assertArrayHasKey('second_setting', $settings);
        $this->assertArrayHasKey('third_setting', $settings);
    }

    /** @test */
    public function it_can_encode_values()
    {
        $this->assertEquals("value", ModelSettings::encodeValue("value"));
    }

    /** @test */
    public function it_can_decode_values()
    {
        $this->assertEquals("value", ModelSettings::decodeValue("value"));
    }

    /** @test */
    public function it_encodes_boolean_values()
    {
        $this->assertEquals("bool:true", ModelSettings::encodeValue(true));
        $this->assertEquals("bool:false", ModelSettings::encodeValue(false));
    }

    /** @test */
    public function it_can_decode_boolean_values()
    {
        $this->assertTrue(is_bool(ModelSettings::decodeValue(
            ModelSettings::encodeValue(Factory::create()->boolean())
        )));

        $settings = $this->settings();

        $settings->set('boolean_setting', true);
        $this->assertTrue($settings->boolean_setting);
        $this->assertTrue(is_bool($settings->boolean_setting));
    }

    /** @test */
    public function it_encodes_settings_on_update()
    {
        $settings = $this->settings();

        $value = true;
        $settings->set('setting', $value);
        $this->assertEquals($value, $settings->setting);

        $value = false;
        $settings->set('setting', $value);
        $this->assertEquals($value, $settings->setting);
    }

    /** @test */
    public function it_encodes_integers()
    {
        $integer = Factory::create()->randomDigit;
        $this->assertEquals("int:" . $integer, ModelSettings::encodeValue($integer));
    }

    /** @test */
    public function it_decodes_integers()
    {
        $this->assertTrue(is_int(ModelSettings::decodeValue(
            ModelSettings::decodeValue(Factory::create()->randomDigit)
        )));

        $settings = $this->settings();

        $settings->set('int_setting', 1);
        $this->assertEquals(1, $settings->int_setting);
        $this->assertTrue(is_int($settings->int_setting));
    }

    /** @test */
    public function it_encodes_boolean_strings_as_booleans()
    {
        $this->assertEquals(ModelSettings::encodeValue(true), ModelSettings::encodeValue("true"));
        $this->assertEquals(ModelSettings::encodeValue(false), ModelSettings::encodeValue("false"));
    }
}