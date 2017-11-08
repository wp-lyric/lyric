<?php

namespace LyricTests\PostTypes;

use LyricTests\LyricTestCase;
use Mockery;
use Brain\Monkey\Functions;
use Lyric\PostTypes\PostTypeRegister;

class PostTypeRegisterTest extends LyricTestCase
{

    public function test_build_names_used_in_post_type_with_single_option()
    {
        $register = new PostTypeRegister('lyric-post-type');

        $this->assertAttributeEquals('lyric-post-types', 'postTypeName', $register);
        $this->assertAttributeEquals('Lyric Post Type', 'singular', $register);
        $this->assertAttributeEquals('Lyric Post Types', 'plural', $register);
        $this->assertAttributeEquals('lyric-post-types', 'slug', $register);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Use valid arguments(string|array) to define post type name
     */
    public function test_should_throw_exception_if_post_type_name_has_invalid_format()
    {
        new PostTypeRegister(true);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Using array the [name] key is required to the post type
     */
    public function test_should_throw_exception_if_required_name_not_exist()
    {
        new PostTypeRegister([]);
    }

    public function test_build_names_used_in_post_type_with_array_option()
    {
        $register = new PostTypeRegister([
            'name' => 'lyric',
            'singular' => 'Lyric Post Type',
            'plural' => 'Lyric Posts',
            'slug' => 'lyric-post',
        ]);

        $this->assertAttributeEquals('lyric', 'postTypeName', $register);
        $this->assertAttributeEquals('Lyric Post Type', 'singular', $register);
        $this->assertAttributeEquals('Lyric Posts', 'plural', $register);
        $this->assertAttributeEquals('lyric-post', 'slug', $register);
    }

    public function test_build_names_used_in_post_type_with_incomplete_array_option()
    {
        $register = new PostTypeRegister([
            'name' => 'lyric post type',
        ]);

        $this->assertAttributeEquals('lyric-post-type', 'postTypeName', $register);
        $this->assertAttributeEquals('Lyric Post Type', 'singular', $register);
        $this->assertAttributeEquals('Lyric Post Types', 'plural', $register);
        $this->assertAttributeEquals('lyric-post-types', 'slug', $register);
    }

    public function test_rename_post_type()
    {
        $register = new PostTypeRegister('lyric-post-type');

        $this->assertAttributeEquals('lyric-post-types', 'postTypeName', $register);
        $this->assertAttributeEquals('Lyric Post Type', 'singular', $register);
        $this->assertAttributeEquals('Lyric Post Types', 'plural', $register);
        $this->assertAttributeEquals('lyric-post-types', 'slug', $register);

        $return = $register->assignNames('new-name');

        $this->assertAttributeEquals('new-names', 'postTypeName', $register);
        $this->assertAttributeEquals('New Name', 'singular', $register);
        $this->assertAttributeEquals('New Names', 'plural', $register);
        $this->assertAttributeEquals('new-names', 'slug', $register);
        $this->assertInstanceOf(PostTypeRegister::class, $return);
    }

    public function test_get_correctly_post_type_name()
    {
        $registerOne = new PostTypeRegister('lyric-post-type');

        $registerTwo = new PostTypeRegister('lyric-post-type');
        $registerTwo->assignNames('lyric-post-type-two');

        $this->assertEquals('lyric-post-types', $registerOne->getName());
        $this->assertEquals('lyric-post-type-twos', $registerTwo->getName());
    }

    public function test_should_set_options()
    {
        $register = new PostTypeRegister('lyric-post-type');

        $return = $register->options([
            'has_archive' => true,
            'supports' => ['title', 'thumbnail']
        ]);

        $this->assertAttributeEquals(
            [
                'has_archive' => true,
                'supports' => ['title', 'thumbnail']
            ],
            'options',
            $register
        );
        $this->assertInstanceOf(PostTypeRegister::class, $return);
    }

    public function test_should_set_labels()
    {
        $register = new PostTypeRegister('lyric-post-type');

        $return = $register->labels([
            'name' => 'Lyric',
            'not_found' => 'Not Found Lyric Post'
        ]);

        $this->assertAttributeEquals(
            [
                'name' => 'Lyric',
                'not_found' => 'Not Found Lyric Post'
            ],
            'labels',
            $register
        );
        $this->assertInstanceOf(PostTypeRegister::class, $return);
    }

    public function test_should_set_slug()
    {
        $register = new PostTypeRegister('lyric-post-type');

        $register->slug('Lyric Posts');

        $this->assertAttributeEquals(
            'lyric-posts',
            'slug',
            $register
        );

        $register->slug('Lyric-Posts', false);

        $this->assertAttributeEquals(
            'Lyric-Posts',
            'slug',
            $register
        );

        $this->assertInstanceOf(PostTypeRegister::class, $register->slug('return-instance'));
    }

    public function test_set_icon()
    {
        $register = new PostTypeRegister('lyric-post-type');

        $this->assertInstanceOf(PostTypeRegister::class, $register->icon('custom-icon'));


        $this->assertAttributeEquals(
            'custom-icon',
            'icon',
            $register
        );
    }


    public function test_return_options_and_label_after_merge_default_and_configured()
    {
        $register = new PostTypeRegister('lyric-post-type');

        $expectedLabels = [
            'name' => 'Lyric',
            'singular_name' => 'Lyric Post Type',
            'menu_name' => 'Lyric Post Types',
            'name_admin_bar' => 'Lyric Post Type',
        ];

        $expectedOptions = [
            'labels' => $expectedLabels,
            'public' => false,
            'menu_icon' => '',
            'menu_position' => 42,
            'rewrite' => [
                'slug' => 'lyric-post-types'
            ],
        ];

        Functions\expect('__')
            ->with(Mockery::type('string'), 'lyric')
            ->andReturnFirstArg();

        $register->options(['public' => false, 'menu_position' => 42]);
        $register->labels(['name' => 'Lyric']);

        $this->assertEquals($expectedLabels, $register->getLabels());
        $this->assertEquals($expectedOptions, $register->getOptions());
    }


    public function test_register_post_type()
    {
        $postTypeName = 'lyric-post-types';

        $finalLabels = [
            'name' => 'Lyric Post Types',
            'singular_name' => 'Lyric Post Type',
            'menu_name' => 'Lyric Post Types',
            'name_admin_bar' => 'Lyric Post Type',
        ];

        $finalOptions = [
            'public' => true,
            'menu_icon' => '',
            'rewrite' => [
                'slug' => 'lyric-post-types'
            ],
            'labels' => $finalLabels,
        ];

        Functions\expect('post_type_exists')
            ->once()
            ->with($postTypeName)
            ->andReturn(false);

        Functions\expect('register_post_type')
            ->once()
            ->with($postTypeName, $finalOptions);

        Functions\when('add_action')
            ->alias(function ($action, $callable) {
                $callable();
            });

        Functions\when('__')
            ->returnArg(1);

        $register = new PostTypeRegister('lyric-post-type');

        $this->assertNull($register->bind());
    }

    public function test_should_bind_wordpress_hook()
    {
        Functions\when('__')
            ->returnArg(1);

        $postTypeName = 'lyric-post-type';
        $register = new PostTypeRegister($postTypeName);

        $register->bind();

        $this->assertTrue(has_action('init', 'function ()', 1));
    }
}

