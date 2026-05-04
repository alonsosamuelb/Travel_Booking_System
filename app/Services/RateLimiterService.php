<?php

namespace App\Services;

class RateLimiterService
{
    private string $storageDirectory;

    public function __construct(?string $storageDirectory = null)
    {
        $this->storageDirectory = $storageDirectory ?? __DIR__ . '/../../storage/ratelimits';
    }

    public function tooManyAttempts(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        $bucket = $this->readBucket($key, $windowSeconds);
        return count($bucket['attempts']) >= $maxAttempts;
    }

    public function hit(string $key, int $windowSeconds): void
    {
        $bucket = $this->readBucket($key, $windowSeconds);
        $bucket['attempts'][] = time();
        $this->writeBucket($key, $bucket);
    }

    public function clear(string $key): void
    {
        $path = $this->bucketPath($key);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    public function retryAfter(string $key, int $windowSeconds): int
    {
        $bucket = $this->readBucket($key, $windowSeconds);
        if (!$bucket['attempts']) {
            return 0;
        }

        $oldest = min($bucket['attempts']);
        return max(0, ($oldest + $windowSeconds) - time());
    }

    private function readBucket(string $key, int $windowSeconds): array
    {
        $path = $this->bucketPath($key);
        $bucket = ['attempts' => []];

        if (is_file($path)) {
            $decoded = json_decode((string) file_get_contents($path), true);
            if (is_array($decoded) && isset($decoded['attempts']) && is_array($decoded['attempts'])) {
                $bucket = $decoded;
            }
        }

        $threshold = time() - $windowSeconds;
        $bucket['attempts'] = array_values(array_filter($bucket['attempts'], static fn ($attempt) => (int) $attempt >= $threshold));
        return $bucket;
    }

    private function writeBucket(string $key, array $bucket): void
    {
        if (!is_dir($this->storageDirectory)) {
            mkdir($this->storageDirectory, 0775, true);
        }

        file_put_contents($this->bucketPath($key), json_encode($bucket));
    }

    private function bucketPath(string $key): string
    {
        return $this->storageDirectory . '/' . sha1($key) . '.json';
    }
}
