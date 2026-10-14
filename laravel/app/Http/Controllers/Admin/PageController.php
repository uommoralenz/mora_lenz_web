<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use App\Support\EventBlocks;
use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Free-form pages (the About page), edited with the same block editor and
 * layout options as an event page. Pages are fixed by Page::SLUGS: they are
 * edited, never created or deleted from the panel.
 */
class PageController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        return response()->json(['data' => $this->present(Page::named($slug))]);
    }

    public function update(Request $request, string $slug): JsonResponse
    {
        $page = Page::named($slug);

        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:20000'],
            'content' => ['nullable'],
            'page_options' => ['nullable'],
            'image' => ['nullable', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.max_kb')],
            'remove_image' => ['boolean'],
        ]);

        foreach (['title', 'description'] as $field) {
            if (array_key_exists($field, $data)) {
                $page->{$field} = $data[$field];
            }
        }

        if (array_key_exists('content', $data)) {
            $blocks = EventBlocks::sanitize($data['content']);
            $page->content = $blocks === [] ? null : $blocks;
        }

        if (array_key_exists('page_options', $data)) {
            $options = EventBlocks::pageOptions($data['page_options']);
            $page->page_options = $options === EventBlocks::PAGE_DEFAULTS ? null : $options;
        }

        if ($request->hasFile('image')) {
            $page->image_url = ImageStore::replace($request->file('image'), 'pages', $page->image_url);
        } elseif (! empty($data['remove_image'])) {
            ImageStore::delete($page->image_url);
            $page->image_url = null;
        }

        $page->save();

        return response()->json(['data' => $this->present($page)]);
    }

    protected function present(Page $page): array
    {
        return [
            'slug' => $page->slug,
            'title' => $page->title,
            'description' => $page->description,
            'content' => $page->content ?? [],
            'page_options' => $page->pageOptions(),
            'image_url' => $page->image_url,
            'updated_at' => $page->updated_at?->toIso8601String(),
        ];
    }
}
