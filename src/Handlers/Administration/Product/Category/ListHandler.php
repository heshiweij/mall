<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-04-25 17:18
 */
namespace Notadd\Mall\Handlers\Administration\Product\Category;

use Illuminate\Support\Collection;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductCategory;

/**
 * Class ListHandler.
 */
class ListHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'order'     => Rule::in([
                'asc',
                'desc',
            ]),
            'page'      => Rule::numeric(),
            'paginate'  => Rule::numeric(),
            'parent_id' => Rule::numeric(),
        ], [
            'order.in'          => '排序规则错误',
            'page.numeric'      => '当前页面必须为数值',
            'paginate.numeric'  => '分页数必须为数值',
            'parent_id.numeric' => '父级分类 ID 必须为数值',
        ]);
        $parent_id = $this->request->input('parent_id', 0);
        $builder = ProductCategory::query();
        $builder->where('parent_id', $parent_id);
        $builder->orderBy('created_at', $this->request->input('order', 'desc'));
        $builder = $builder->paginate($this->request->input('paginate', 20));
        if ($parent_id == 0) {
            $category = new \stdClass();
            $level = 1;
        } else {
            $category = ProductCategory::query()->withCount('parent')->find($parent_id);
            if ($category->getAttribute('parent_count')) {
                $level = 3;
            } else {
                $level = 2;
            }
        }
        $this->withCode(200)->withData($this->reformatData($builder->items()))->withExtra([
            'category'   => $category,
            'level'      => $level,
            'pagination' => [
                'total'         => $builder->total(),
                'per_page'      => $builder->perPage(),
                'current_page'  => $builder->currentPage(),
                'last_page'     => $builder->lastPage(),
                'next_page_url' => $builder->nextPageUrl(),
                'prev_page_url' => $builder->previousPageUrl(),
                'from'          => $builder->firstItem(),
                'to'            => $builder->lastItem(),
            ],
            'structure'  => $this->restructureData($builder->items()),
        ])->withMessage('获取商品列表成功！');
    }

    /**
     * @param array $items
     *
     * @return array
     */
    protected function reformatData(array $items)
    {
        $data = new Collection();
        collect($items)->each(function (ProductCategory $category) use ($data) {
            $data->put($category->getAttribute('id'), $category);
        });

        return $data->toArray();
    }

    /**
     * @param array $items
     *
     * @return array
     */
    protected function restructureData(array $items)
    {
        $data = new Collection();
        $items = collect($items);
        $items->where('parent_id', 0)->each(function (ProductCategory $category) use ($data, $items) {
            $children = new Collection();
            $items->where('parent_id', $category->getAttribute('id'))->each(function (ProductCategory $category) use ($children, $items) {
                $sub = new Collection();
                $items->where('parent_id', $category->getAttribute('id'))->each(function (ProductCategory $category) use ($sub) {
                    $sub->put($category->getAttribute('id'), $category);
                });
                $category->setAttribute('children', $sub->toArray());
                $children->put($category->getAttribute('id'), $category);
            });
            $category->setAttribute('children', $children->toArray());
            $data->put($category->getAttribute('id'), $category);
        });

        return $data->toArray();
    }
}
