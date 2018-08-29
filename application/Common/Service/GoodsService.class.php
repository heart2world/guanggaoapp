<?php
/**
 * Created by PhpStorm
 * Date: 2016/11/10
 * Time: 11:24
 */
namespace Common\Service;
use Common\Model\GoodsModel;
class GoodsService
{
    /**
     * @var GoodsModel
     */
    private $_goodsModel;




    public function __construct()
    {


        if (empty($this->_commodityModel)) {

            $this->_goodsModel = new GoodsModel();
        }


    }

    public function getList($where = [], $pageSize = 15)
    {
        $rel = $this->_goodsModel->getList($where, $pageSize, 'id ASC');
        return $rel;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function doSave($data)
    {
        $result=$this->_goodsModel->doSave($data);
        return $result;
    }



}