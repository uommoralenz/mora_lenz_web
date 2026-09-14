<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait ReordersRecords
{
    /**
     * Apply a new display order sent as [{id, sort_order}, ...].
     *
     * Wrapped in a transaction so a half-applied order can never be left behind
     * if one of the rows fails to save.
     */
    protected function applyOrder(Request $request, string $modelClass): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($data, $modelClass) {
            foreach ($data['items'] as $item) {
                $modelClass::where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }
        });

        return response()->json(['message' => 'Order updated.']);
    }

    /** Next sort_order value for a new row, so it lands at the end of the list. */
    protected function nextSortOrder(string $modelClass, array $scope = []): int
    {
        $query = $modelClass::query();

        foreach ($scope as $column => $value) {
            $query = $value === null
                ? $query->whereNull($column)
                : $query->where($column, $value);
        }

        return (int) $query->max('sort_order') + 1;
    }
}
