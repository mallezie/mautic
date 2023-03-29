<?php

namespace Mautic\LeadBundle\Tests\Helper;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\PathsHelper;
use Mautic\CoreBundle\Twig\Helper\AssetsHelper;
use Mautic\CoreBundle\Twig\Helper\GravatarHelper;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Twig\Helper\AvatarHelper;
use Mautic\LeadBundle\Twig\Helper\DefaultAvatarHelper;
use Symfony\Component\Asset\Packages;

class AvatarHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AssetsHelper
     */
    private $assetsHelperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|PathsHelper
     */
    private $pathsHelperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|GravatarHelper
     */
    private $gravatarHelperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|DefaultAvatarHelper
     */
    private $defaultAvatarHelperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Lead
     */
    private $leadMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|AvatarHelper
     */
    private $avatarHelper;

    protected function setUp(): void
    {
        $packagesMock = $this->getMockBuilder(Packages::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->assetsHelperMock        = new AssetsHelper($packagesMock);
        $this->pathsHelperMock         = $this->createMock(PathsHelper::class);
        $this->defaultAvatarHelperMock = new DefaultAvatarHelper($this->pathsHelperMock, $this->assetsHelperMock);
        $this->gravatarHelperMock      = new GravatarHelper($this->defaultAvatarHelperMock, $this->createMock(CoreParametersHelper::class), $this->createMock(RequestStack::class));
        $this->leadMock                = $this->createMock(Lead::class);
        $this->avatarHelper            = new AvatarHelper($this->assetsHelperMock, $this->pathsHelperMock, $this->gravatarHelperMock, $this->defaultAvatarHelperMock);
    }

    /**
     * Test to get gravatar.
     */
    public function testGetAvatarWhenGravatar()
    {
        $this->leadMock->method('getPreferredProfileImage')
            ->willReturn('gravatar');
        $this->leadMock->method('getSocialCache')
            ->willReturn([]);
        $this->leadMock->method('getEmail')
            ->willReturn('mautic@acquia.com');
        $this->gravatarHelperMock->method('getImage')
            ->with('mautic@acquia.com')
            ->willReturn('gravatarImage');
        $avatar = $this->avatarHelper->getAvatar($this->leadMock);
        $this->assertSame('gravatarImage', $avatar, 'Gravatar image should be returned');
    }

    /**
     * Test to get default image.
     */
    public function testGetAvatarWhenDefault()
    {
        $this->leadMock->method('getPreferredProfileImage')
            ->willReturn('gravatar');
        $this->leadMock->method('getSocialCache')
            ->willReturn([]);
        $this->leadMock->method('getEmail')
            ->willReturn('');
        $this->defaultAvatarHelperMock->method('getDefaultAvatar')
            ->willReturn('defaultImage');
        $avatar = $this->avatarHelper->getAvatar($this->leadMock);
        $this->assertSame('defaultImage', $avatar, 'Default image image should be returned');
    }
}
