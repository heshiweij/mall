<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 13:21
 */
namespace Notadd\Mall\Handlers\User\Order;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Mall\Models\Order;

/**
 * Class CancelHandler.
 */
class CancelHandler extends Handler
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
            'id.required' => '订单 ID 必须填写',
            'id.numeric'  => '订单 ID 必须为数值',
        ]);
        $this->beginTransaction();
        $order = Order::query()->find($this->request->input('id'));
        $data = $this->request->only([]);
        if ($order instanceof Order && $order->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('编辑订单信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的订单信息！');
        }
    }
}
