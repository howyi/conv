<?php

namespace Laminaria\Conv\Generator;

use Laminaria\Conv\Util\FieldOrder;

class FieldOrderGenerator
{
    public static function generate(array $before, array $after): array
    {
        $startIndex = null;
        foreach ($after as $afterKey => $field) {
            $beforeKey = array_search($field, $before);
            if ($beforeKey !== $afterKey) {
                $startIndex = $afterKey;
                break;
            }
        }

        $endIndex = null;
        foreach (array_reverse($after, true) as $afterKey => $field) {
            $beforeKey = array_search($field, $before);
            if ($beforeKey !== $afterKey) {
                $endIndex = $afterKey;
                break;
            }
        }

        if (is_null($startIndex) or is_null($endIndex)) {
            return [];
        }

        $rangedBefore = array_slice($before, $startIndex, $endIndex - $startIndex + 1);
        $rangedAfter = array_slice($after, $startIndex, $endIndex - $startIndex + 1);


        $sides = [];
        foreach ($rangedBefore as $center) {
            $beforeLeftSide = array_slice(
                $rangedBefore,
                0,
                array_search($center, $rangedBefore)
            );
            $afterLeftSide = array_slice(
                $rangedAfter,
                0,
                array_search($center, $rangedAfter)
            );
            $leftSide = self::oneside($beforeLeftSide, $afterLeftSide);

            $beforeRightSide = array_slice(
                $rangedBefore,
                array_search($center, $rangedBefore) + 1,
                count($rangedBefore) - 1
            );
            $afterRightSide = array_slice(
                $rangedAfter,
                array_search($center, $rangedAfter) + 1,
                count($rangedBefore) - 1
            );
            $rightSide = self::oneside($beforeRightSide, $afterRightSide);

            list($bothSide, $num) = self::bothsides($leftSide, $center, $rightSide);
            $sides[$num] = $bothSide;
        }

        $ranged = $sides[min(array_keys($sides))];

        $movedFieldOrderList = [];
        foreach ($ranged as $value) {
            if (!empty($value['before']) and !empty($value['after'])) {
                continue;
            }

            $field = $value['before'] ?? $value['after'];

            if (array_key_exists($field, $movedFieldOrderList)) {
                continue;
            }

            $beforeKey = array_search($field, $before, true) - 1;
            $afterKey = array_search($field, $after, true) - 1;
            $movedFieldOrderList[$field] = new FieldOrder(
                $field,
                array_key_exists($beforeKey, $before) ? $before[$beforeKey] : null,
                array_key_exists($afterKey, $after) ? $after[$afterKey] : null
            );
        }

        return $movedFieldOrderList;
    }

    public static function oneside(array $before, array $after): array
    {
        $side = [];
        while (!(empty($before) and empty($after))) {
            if (current($before) === current($after)) {
                $side[] = ['before' => current($before),'after' => current($after)];
                $before = array_diff($before, [current($before)]);
                $after = array_diff($after, [current($after)]);
                continue;
            }

            if (empty($before)) {
                $side[] = ['before' => null, 'after' => current($after)];
                $after = array_diff($after, [current($after)]);
                continue;
            }
            if (empty($after)) {
                $side[] = ['before' => current($before),'after' => null];
                $before = array_diff($before, [current($before)]);
                continue;
            }

            if (array_search(current($before), $after) and !array_search(current($after), $before)) {
                $side[] = ['before' => null, 'after' => current($after)];
                $after = array_diff($after, [current($after)]);
                continue;
            }
            if (array_search(current($after), $before) and !array_search(current($before), $after)) {
                $side[] = ['before' => current($before),'after' => null];
                $before = array_diff($before, [current($before)]);
                continue;
            }

            $beforeKey = array_search(current($before), $before);
            $afterKey = array_search(current($after), $after);
            if ($beforeKey <= $afterKey) {
                $side[] = ['before' => current($before),'after' => null];
                $before = array_diff($before, [current($before)]);
            } else {
                $side[] = ['before' => null, 'after' => current($after)];
                $after = array_diff($after, [current($after)]);
            }
        }
        return $side;
    }

    public static function bothsides(array $left, string $center, array $right): array
    {
        $bothSide = array_merge(
            $left,
            [
                ['before' => $center, 'after' => $center]
            ],
            $right
        );

        $count = 0;
        foreach ($bothSide as $value) {
            if (is_null($value['before'])) {
                $count++;
            }
        }
        return [$bothSide, $count];
    }
}
