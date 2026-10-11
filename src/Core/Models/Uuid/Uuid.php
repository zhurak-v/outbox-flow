<?php

declare(strict_types=1);

namespace OutboxFlow\Core\Models\Uuid;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonSerializable;
use LogicException;
use OutboxFlow\Core\Models\Equatable\Equatable;
use OutboxFlow\Core\Models\Hashable\Hashable;
use Stringable;

final class Uuid implements Equatable, Hashable, JsonSerializable, Stringable
{

    private const string PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
    private const int MAX_TIMESTAMP = 0xFFFFFFFFFFFF;

    private static int $lastTimestamp = 0;
    private static int $sequence = 0;

    private int $mostSignificantBits;
    private int $leastSignificantBits;

    public function __construct(int $mostSignificantBits, int $leastSignificantBits)
    {
        $this->mostSignificantBits = $mostSignificantBits;
        $this->leastSignificantBits = $leastSignificantBits;
    }

    public static function nil(): self
    {
        return new self(0, 0);
    }

    public static function max(): self
    {
        return new self(-1, -1);
    }

    public static function v3(self $namespace, string $name): self
    {
        return self::hashed($namespace, $name, 3, 'md5');
    }

    public static function v4(): self
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return self::fromBytes($bytes);
    }

    public static function v5(self $namespace, string $name): self
    {
        return self::hashed($namespace, $name, 5, 'sha1');
    }

    public static function v7(?int $timestampMs = null): self
    {
        if ($timestampMs !== null) {
            return self::buildV7($timestampMs, random_int(0, 0xfff));
        }

        $now = (int) (microtime(true) * 1000);

        if ($now > self::$lastTimestamp) {
            self::$lastTimestamp = $now;
            self::$sequence = random_int(0, 0x7ff);
        } else {
            self::$sequence++;

            if (self::$sequence > 0xfff) {
                self::$lastTimestamp++;
                self::$sequence = 0;
            }
        }

        return self::buildV7(self::$lastTimestamp, self::$sequence);
    }

    public static function fromBytes(string $bytes): self
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException('UUID must be exactly 16 bytes');
        }

        [$most, $least] = array_values(unpack('J2', $bytes));

        return new self($most, $least);
    }

    public static function fromString(string $value): self
    {
        if (!self::isValid($value)) {
            throw new InvalidArgumentException(sprintf('Invalid UUID string "%s"', $value));
        }

        return self::fromBytes(hex2bin(str_replace('-', '', $value)));
    }

    public static function tryFromString(string $value): ?self
    {
        return self::isValid($value) ? self::fromString($value) : null;
    }

    public static function isValid(string $value): bool
    {
        return preg_match(self::PATTERN, $value) === 1;
    }

    public function getMostSignificantBits(): int
    {
        return $this->mostSignificantBits;
    }

    public function getLeastSignificantBits(): int
    {
        return $this->leastSignificantBits;
    }

    public function version(): int
    {
        return ($this->mostSignificantBits >> 12) & 0xf;
    }

    public function variant(): int
    {
        $least = $this->leastSignificantBits;

        if ((($least >> 63) & 1) === 0) {
            return 0;
        }

        if ((($least >> 62) & 1) === 0) {
            return 2;
        }

        if ((($least >> 61) & 1) === 0) {
            return 6;
        }

        return 7;
    }

    public function timestamp(): int
    {
        if ($this->version() !== 7) {
            throw new LogicException('Timestamp is available only for UUID v7');
        }

        return ($this->mostSignificantBits >> 16) & self::MAX_TIMESTAMP;
    }

    public function dateTime(): DateTimeImmutable
    {
        $timestamp = $this->timestamp();

        return DateTimeImmutable::createFromFormat(
            'U.v',
            sprintf('%d.%03d', intdiv($timestamp, 1000), $timestamp % 1000),
        );
    }

    public function isNil(): bool
    {
        return $this->mostSignificantBits === 0 && $this->leastSignificantBits === 0;
    }

    public function isMax(): bool
    {
        return $this->mostSignificantBits === -1 && $this->leastSignificantBits === -1;
    }

    public function toBytes(): string
    {
        return pack('JJ', $this->mostSignificantBits, $this->leastSignificantBits);
    }

    public function toHex(): string
    {
        return bin2hex($this->toBytes());
    }

    public function hash(): int
    {
        $mixed = $this->mostSignificantBits ^ $this->leastSignificantBits;
        $folded = (($mixed >> 32) ^ $mixed) & 0xFFFFFFFF;

        return $folded >= 0x80000000 ? $folded - 0x100000000 : $folded;
    }

    public function equals(object $equatable): bool
    {
        return $equatable instanceof self
            && $this->mostSignificantBits === $equatable->mostSignificantBits
            && $this->leastSignificantBits === $equatable->leastSignificantBits;
    }

    public function compareTo(self $other): int
    {
        return [
                $this->mostSignificantBits ^ PHP_INT_MIN,
                $this->leastSignificantBits ^ PHP_INT_MIN,
            ] <=> [
                $other->mostSignificantBits ^ PHP_INT_MIN,
                $other->leastSignificantBits ^ PHP_INT_MIN,
            ];
    }

    public function toString(): string
    {
        $hex = $this->toHex();

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12),
        );
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    private static function buildV7(int $timestampMs, int $sequence): self
    {
        if ($timestampMs < 0 || $timestampMs > self::MAX_TIMESTAMP) {
            throw new InvalidArgumentException('Timestamp must fit into 48 bits');
        }

        $tail = random_bytes(8);
        $tail[0] = chr((ord($tail[0]) & 0x3f) | 0x80);

        return self::fromBytes(
            substr(pack('J', $timestampMs), 2, 6)
            . pack('n', 0x7000 | ($sequence & 0xfff))
            . $tail,
        );
    }

    private static function hashed(self $namespace, string $name, int $version, string $algorithm): self
    {
        $hash = substr(hash($algorithm, $namespace->toBytes() . $name, true), 0, 16);
        $hash[6] = chr((ord($hash[6]) & 0x0f) | ($version << 4));
        $hash[8] = chr((ord($hash[8]) & 0x3f) | 0x80);

        return self::fromBytes($hash);
    }

}