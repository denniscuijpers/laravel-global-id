<?php

declare(strict_types=1);

namespace DennisCuijpers\GlobalId;

class GlobalId
{
    private const DAMM_TABLE = [
        [0, 3, 1, 7, 5, 9, 8, 6, 4, 2],
        [7, 0, 9, 2, 1, 5, 4, 8, 6, 3],
        [4, 2, 0, 6, 8, 7, 1, 3, 5, 9],
        [1, 7, 5, 0, 9, 8, 3, 4, 2, 6],
        [6, 1, 2, 3, 0, 4, 5, 9, 7, 8],
        [3, 6, 7, 4, 2, 0, 9, 5, 8, 1],
        [5, 8, 6, 9, 7, 2, 0, 1, 3, 4],
        [8, 9, 4, 5, 3, 6, 2, 0, 1, 7],
        [9, 4, 3, 8, 6, 1, 7, 2, 0, 5],
        [2, 5, 8, 1, 4, 3, 6, 7, 9, 0],
    ];

    public function __construct(private array $config)
    {
    }

    public function encode(string $class, int $id): int
    {
        $index = array_search($class, $this->config['map'], true);

        if ($index === false || $id < 1) {
            return -1;
        }

        $number = $id * 10 ** $this->config['digits'] + $index;

        if ($this->config['check']) {
            $number = $number * 10 + $this->check($number);
        }

        return $number;
    }

    public function decode(int $gid): ?array
    {
        if ($this->config['check']) {
            $check = $gid % 10;
            $gid   = intdiv($gid, 10);

            if ($this->check($gid) !== $check) {
                return null;
            }
        }

        $id    = intdiv($gid, 10 ** $this->config['digits']);
        $index = $gid % 10 ** $this->config['digits'];
        $class = $this->config['map'][$index] ?? null;

        if ($class === null || $id < 1) {
            return null;
        }

        return [$class, $id];
    }

    public function class(int $gid): ?string
    {
        return $this->decode($gid)[0] ?? null;
    }

    public function id(int $gid): ?int
    {
        return $this->decode($gid)[1] ?? null;
    }

    public function make($object): int
    {
        if (!is_object($object)) {
            return -1;
        }

        return $this->encode($object::class, $object->id ?? 0);
    }

    public function find(int $gid)
    {
        [$class, $id] = static::decode($gid);

        if ($class === null || $id === null) {
            return null;
        }

        return $class::find($id);
    }

    private function check(int $number): int
    {
        $digits  = str_split((string) $number);
        $interim = 0;

        foreach ($digits as $digit) {
            $interim = self::DAMM_TABLE[$interim][(int) $digit];
        }

        return $interim;
    }
}
