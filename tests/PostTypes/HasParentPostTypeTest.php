<?php

namespace LyricTests\PostTypes;

use Lyric\PostTypes\HasParentPostType;
use PHPUnit\Framework\TestCase;
use Mockery;

class HasParentPostTypeTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Register parent post type if is string
     */
    public function testRegisterParentPostTypeIfIsString()
    {
        $hasParentPostType = new HasParentPostTypeDummy();

        $hasParentPostType->setPostType('lyric-post-type');

        $this->assertAttributeEquals('lyric-post-type', 'postType', $hasParentPostType);
    }

    /**
     * Should register parent post type if is register instance
     */
    public function testShouldRegisterParentPostTypeIfIsRegisterInstance()
    {
        $register = Mockery::mock(\Lyric\Contracts\PostTypes\PostTypeRegister::class);

        $register->shouldReceive('getName')
            ->once()
            ->withNoArgs()
            ->andReturn('lyric-post-type');

        $hasParentPostType = new HasParentPostTypeDummy();

        $hasParentPostType->setPostType($register);

        $this->assertAttributeEquals('lyric-post-type', 'postType', $hasParentPostType);
    }

    /**
     * Should_register_parent_post_type_if_is_base_instance
     */
    public function testShouldRegisterParentPostTypeIfIsBaseInstance()
    {
        $register = Mockery::mock(\Lyric\Contracts\PostTypes\PostTypeBase::class);

        $register->shouldReceive('getPostTypeName')
            ->once()
            ->withNoArgs()
            ->andReturn('lyric-post-type');

        $hasParentPostType = new HasParentPostTypeDummy();

        $hasParentPostType->setPostType($register);

        $this->assertAttributeEquals('lyric-post-type', 'postType', $hasParentPostType);
    }


}

class HasParentPostTypeDummy
{
    use HasParentPostType;
}
