<?php

namespace App\Tests\Service;

use App\Service\MediaPathResolver;
use PHPUnit\Framework\TestCase;

final class MediaPathResolverTest extends TestCase
{
    private string $projectDir;

    protected function setUp(): void
    {
        $this->projectDir = dirname(__DIR__, 2);
    }

    public function testResolveKeepsRemoteTeamLogoUrl(): void
    {
        $resolver = new MediaPathResolver($this->projectDir);

        self::assertSame([
            'kind' => 'url',
            'value' => 'https://crests.football-data.org/57.png',
        ], $resolver->resolve('https://crests.football-data.org/57.png', 'equipes'));
    }

    public function testResolveProxiesAllowedExternalPlayerAvatar(): void
    {
        $homeDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sport-insight-media-test';
        $avatarDir = $homeDir . DIRECTORY_SEPARATOR . '.sport-insight' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . 'joueurs';
        $avatarPath = $avatarDir . DIRECTORY_SEPARATOR . 'player.png';

        if (!is_dir($avatarDir)) {
            mkdir($avatarDir, 0777, true);
        }
        file_put_contents($avatarPath, 'avatar');

        $previousUserProfile = $_SERVER['USERPROFILE'] ?? null;
        $_SERVER['USERPROFILE'] = $homeDir;

        try {
            $resolver = new MediaPathResolver($this->projectDir);
            $resolved = $resolver->resolve($avatarPath, 'joueurs');

            self::assertIsArray($resolved);
            self::assertSame('proxy', $resolved['kind']);
            self::assertSame(
                str_replace('\\', '/', realpath($avatarPath) ?: $avatarPath),
                $resolver->decodeProxyPath($resolved['value'], 'joueurs')
            );
        } finally {
            if ($previousUserProfile === null) {
                unset($_SERVER['USERPROFILE']);
            } else {
                $_SERVER['USERPROFILE'] = $previousUserProfile;
            }

            @unlink($avatarPath);
            @rmdir($avatarDir);
            @rmdir(dirname($avatarDir));
            @rmdir(dirname(dirname($avatarDir)));
            @rmdir($homeDir);
        }
    }
}
