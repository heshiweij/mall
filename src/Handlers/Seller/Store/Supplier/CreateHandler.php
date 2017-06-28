<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-27 17:18
 */
namespace Notadd\Mall\Handlers\Seller\Store\Supplier;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Mall\Models\StoreSupplier;

/**
 * Class CreateHandler.
 */
class CreateHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'store_id'  => 'required|numeric',
            'name'      => 'required',
            'contacts'  => 'required',
            'telephone' => 'required',
        ], [
            'store_id.numeric'   => '店铺 ID 必须为数值',
            'store_id.required'  => '店铺 ID 必须填写',
            'name.required'      => '供货商名称必须填写',
            'contacts.required'  => '联系人必须填写',
            'telephone.required' => '联系电话必须填写',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'store_id',
            'name',
            'contacts',
            'telephone',
            'comments',
        ]);
        if (StoreSupplier::query()->create($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('创建供应商成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('创建供应商失败！');
        }
    }
}
