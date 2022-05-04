<?php

declare(strict_types=1);

namespace DennisCuijpers\GlobalId\Facades;

use DennisCuijpers\GlobalId\GlobalId;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array map(array $map = null)
 * @method static int encode(string $class, int $id)
 * @method static array|null decode(int $gid)
 * @method static string|null class(int $gid)
 * @method static int|null id(int $gid)
 * @method static int|null make($object)
 * @method static find(int $gid)
 *
 * @see GlobalId
 */
class Gid extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'global_id';
    }
}
