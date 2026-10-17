<?php

declare(strict_types=1);

namespace OutboxFlow\Core\Models\Buffer;

use InvalidArgumentException;
use OutOfBoundsException;

final class BytesBuffer implements MutableBuffer
{

    private string $bytes;

    private int $length;

    public function __construct(int $initialCapacity = 0)
    {
        if ($initialCapacity < 0) {
            throw new InvalidArgumentException(
                'Capacity must be non-negative, got ' . $initialCapacity . '.'
            );
        }

        $this->bytes = str_repeat("\0", $initialCapacity);
        $this->length = 0;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getCapacity(): int
    {
        return strlen($this->bytes);
    }

    public function getByte(int $index): int
    {
        $this->assertIndex($index);

        return ord($this->bytes[$index]);
    }

    public function getBytes(int $index, int $length): string
    {
        $this->assertRange($index, $length);

        return substr($this->bytes, $index, $length);
    }

    public function isEmpty(): bool
    {
        return $this->length === 0;
    }

    public function equals(object $equatable): bool
    {
        if ($this === $equatable) {
            return true;
        }

        if (!$equatable instanceof Buffer) {
            return false;
        }

        if ($this->length !== $equatable->getLength()) {
            return false;
        }

        return $this->getBytes(0, $this->length)
            === $equatable->getBytes(0, $this->length);
    }

    public function hash(): int
    {
        return crc32(substr($this->bytes, 0, $this->length));
    }

    public function putByte(int $index, int $byte): void
    {
        $this->assertIndex($index);
        $this->assertByte($byte);

        $this->bytes[$index] = chr($byte);
    }

    public function putBytes(int $index, string $bytes): void
    {
        $length = strlen($bytes);

        $this->assertRange($index, $length);

        for ($i = 0; $i < $length; $i++) {
            $this->bytes[$index + $i] = $bytes[$i];
        }
    }

    public function appendByte(int $byte): void
    {
        $this->assertByte($byte);
        $this->ensureCapacity($this->length + 1);

        $this->bytes[$this->length] = chr($byte);
        $this->length++;
    }

    public function appendBytes(string $bytes): void
    {
        $count = strlen($bytes);

        if ($count === 0) {
            return;
        }

        $this->ensureCapacity($this->length + $count);

        for ($i = 0; $i < $count; $i++) {
            $this->bytes[$this->length + $i] = $bytes[$i];
        }

        $this->length += $count;
    }

    public function setLength(int $length): void
    {
        if ($length < 0) {
            throw new InvalidArgumentException('Length must be non-negative, got ' . $length . '.');
        }

        if ($length > $this->length) {
            $this->ensureCapacity($length);

            for ($i = $this->length; $i < $length; $i++) {
                $this->bytes[$i] = "\0";
            }
        }

        $this->length = $length;
    }

    public function clear(): void
    {
        $this->length = 0;
    }

    public static function fromString(string $bytes): BytesBuffer
    {
        $buffer = new self(strlen($bytes));
        $buffer->appendBytes($bytes);

        return $buffer;
    }

    private function ensureCapacity(int $required): void
    {
        $capacity = $this->getCapacity();

        if ($required <= $capacity) {
            return;
        }

        $this->bytes .= str_repeat("\0", max($required, $capacity * 2) - $capacity);
    }

    private function assertIndex(int $index): void
    {
        if ($index < 0 || $index >= $this->length) {
            throw new OutOfBoundsException(
                'Index ' . $index . ' is out of bounds for buffer of length ' . $this->length . '.'
            );
        }
    }

    private function assertRange(int $index, int $length): void
    {
        if ($index < 0 || $length < 0 || $index + $length > $this->length) {
            throw new OutOfBoundsException(
                'Range [' . $index . ', ' . ($index + $length) . ') is out of bounds for buffer of length '
                . $this->length . '.'
            );
        }
    }

    private function assertByte(int $byte): void
    {
        if ($byte < 0 || $byte > 255) {
            throw new InvalidArgumentException('Byte must be in range 0..255, got ' . $byte . '.');
        }
    }

}