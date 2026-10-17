<?php

namespace OutboxFlow\Core\Models\Buffer;

interface MutableBuffer extends Buffer
{

    public function putByte(int $index, int $byte): void;

    public function putBytes(int $index, string $bytes): void;

    public function appendByte(int $byte): void;

    public function appendBytes(string $bytes): void;

    public function setLength(int $length): void;

    public function clear(): void;

}