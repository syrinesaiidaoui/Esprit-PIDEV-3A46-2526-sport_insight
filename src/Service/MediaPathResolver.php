<?php

namespace App\Service;

final class MediaPathResolver
{
    private const TYPE_PUBLIC_DIRECTORIES = [
        'equipes' => 'uploads/equipes',
        'joueurs' => 'uploads/joueurs',
    ];

    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    /**
     * @return array{kind: 'url'|'asset'|'proxy', value: string}|null
     */
    public function resolve(?string $storedValue, string $type): ?array
    {
        $type = $this->normalizeType($type);
        if ($type === null) {
            return null;
        }

        $value = trim((string) $storedValue);
        if ($value === '') {
            return null;
        }

        if ($this->isRemoteUrl($value)) {
            return [
                'kind' => 'url',
                'value' => $value,
            ];
        }

        if ($this->isAbsolutePath($value)) {
            $absolutePath = $this->normalizeRealPath($value);
            if ($absolutePath === null) {
                return null;
            }

            $publicRelativePath = $this->toPublicRelativePath($absolutePath);
            if ($publicRelativePath !== null) {
                return [
                    'kind' => 'asset',
                    'value' => $publicRelativePath,
                ];
            }

            if (!$this->isAllowedExternalPath($absolutePath, $type)) {
                return null;
            }

            return [
                'kind' => 'proxy',
                'value' => $this->encodePath($absolutePath),
            ];
        }

        $relativeValue = ltrim(str_replace('\\', '/', $value), '/');
        if ($relativeValue === '') {
            return null;
        }

        if (preg_match('~^uploads/(equipes|joueurs)/~i', $relativeValue) === 1) {
            return [
                'kind' => 'asset',
                'value' => $relativeValue,
            ];
        }

        return [
            'kind' => 'asset',
            'value' => self::TYPE_PUBLIC_DIRECTORIES[$type] . '/' . $relativeValue,
        ];
    }

    public function decodeProxyPath(string $encodedPath, string $type): ?string
    {
        $type = $this->normalizeType($type);
        if ($type === null) {
            return null;
        }

        $padding = strlen($encodedPath) % 4;
        if ($padding > 0) {
            $encodedPath .= str_repeat('=', 4 - $padding);
        }

        $decodedPath = base64_decode(strtr($encodedPath, '-_', '+/'), true);
        if (!is_string($decodedPath) || $decodedPath === '') {
            return null;
        }

        $absolutePath = $this->normalizeRealPath($decodedPath);
        if ($absolutePath === null) {
            return null;
        }

        if (!$this->isAllowedExternalPath($absolutePath, $type)) {
            return null;
        }

        return $absolutePath;
    }

    private function normalizeType(string $type): ?string
    {
        return array_key_exists($type, self::TYPE_PUBLIC_DIRECTORIES) ? $type : null;
    }

    private function isRemoteUrl(string $value): bool
    {
        return preg_match('~^(https?:)?//~i', $value) === 1;
    }

    private function isAbsolutePath(string $value): bool
    {
        return preg_match('~^[A-Za-z]:[\\\\/]~', $value) === 1 || str_starts_with($value, '/');
    }

    private function normalizeRealPath(string $path): ?string
    {
        $realPath = realpath($path);
        if ($realPath === false || !is_file($realPath)) {
            return null;
        }

        return str_replace('\\', '/', $realPath);
    }

    private function toPublicRelativePath(string $absolutePath): ?string
    {
        $publicRoot = str_replace('\\', '/', realpath($this->projectDir . '/public') ?: $this->projectDir . '/public');
        $prefix = rtrim($publicRoot, '/') . '/';

        if (!str_starts_with($absolutePath, $prefix)) {
            return null;
        }

        return substr($absolutePath, strlen($prefix));
    }

    private function isAllowedExternalPath(string $absolutePath, string $type): bool
    {
        foreach ($this->allowedExternalRoots($type) as $root) {
            if ($root !== '' && ($absolutePath === $root || str_starts_with($absolutePath, $root . '/'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    private function allowedExternalRoots(string $type): array
    {
        $roots = [];

        $publicUploads = realpath($this->projectDir . '/public/' . self::TYPE_PUBLIC_DIRECTORIES[$type]);
        if ($publicUploads !== false) {
            $roots[] = str_replace('\\', '/', $publicUploads);
        }

        $userHome = $_SERVER['USERPROFILE'] ?? $_SERVER['HOME'] ?? '';
        if ($userHome === '') {
            $userHome = getenv('USERPROFILE') ?: getenv('HOME') ?: '';
        }
        if (is_string($userHome) && $userHome !== '') {
            $avatarRoot = realpath($userHome . DIRECTORY_SEPARATOR . '.sport-insight' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $type);
            if ($avatarRoot !== false) {
                $roots[] = str_replace('\\', '/', $avatarRoot);
            }
        }

        return $roots;
    }

    private function encodePath(string $absolutePath): string
    {
        return rtrim(strtr(base64_encode($absolutePath), '+/', '-_'), '=');
    }
}
