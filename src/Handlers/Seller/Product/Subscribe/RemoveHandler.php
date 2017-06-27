<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 19:59
 */
namespace Notadd\Mall\Handlers\Seller\Product\Subscribe;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Mall\Models\ProductSubscribe;

/**
 * Class RemoveHandler.
 */
class RemoveHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    public function execute()
    {
        $this->validate($this->request, [
            'id' => 'required|numeric',
        ], [
            'id.required' => '订阅 ID 必须填写',
            'id.numeric'  => '订阅 ID 必须为数值',
        ]);
        $this->beginTransaction();
        $subscribe = ProductSubscribe::query()->find($this->request->input('id'));
        if ($subscribe instanceof ProductSubscribe && $subscribe->delete()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('删除订阅信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的订阅信息！');
        }
    }
}
