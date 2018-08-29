<?php
/**
 *   接口
 */
namespace Home\Controller;
use Common\Model\CartModel;
use Common\Model\OrderGoodsModel;
use Common\Model\OrderModel;
use Common\Model\UserAddressModel;

class IndexController extends BaseController {
    public function index(){
        
	}
	public function showshop(){
        $forget = I('get.');
    	if($forget['token'] != C('gg_token'))
    	{
    		$this->ajaxReturn(array('msg'=>'token验证失败','status'=>'201','data'=>null));
    	}
		$list['atype'] =1;
		$this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$list));
	}

	// 轮播图列表
	public function banner()
	{
    	$list =M('banner')->field('id,url_link,post_img,atype')->where("status=1")->order('sortorder asc')->select();
    	
    	foreach ($list as $key => $value) {
    		if($value['post_img'])
    		{
    			$list[$key]['post_img'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$value['post_img'];
    		}
			if($value['url_link'] == '')
            {
                $list[$key]['url_link'] ='0';
            }
    	}
		
    	$this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$list));
	}

	// 链接轮播图详情接口
	public function bannerdetail()
	{
        $forget = I('get.');
		$info =M('banner')->field('id,post_content')->where("status=1 and id='".intval($forget['id'])."'")->find();
		if(empty($info))
		{
			$this->ajaxReturn(array('msg'=>'轮播图已下架','status'=>'202','data'=>null));
		}else
		{
			$this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$info));
		}
	}

	// 分类接口
	public function catlist()
	{
    	$list =M('category')->field('id,catname,catimg')->where("status=1")->order('sortorder asc')->select();
    	
    	foreach ($list as $key => $value) {
    		if($value['catimg'])
    		{
    			$list[$key]['catimg'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$value['catimg'];
    		}
    	}
		
    	$this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$list));
	}
	
	// 推荐商品接口
	public function commondgoods()
	{
		$forget = I('get.');
		$page = $forget['page'] ? $forget['page']: '1';
		$count =M('goods')->field('id,goodsname,thumb_img,price,unit')->where("issale=1 and isindex=1")->order('sortorder asc')->count();
    	$list =M('goods')->field('id,goodsname,thumb_img,price,goodsbrief,catid,unit')->where("issale=1 and isindex=1")->order('sortorder asc')->page($page.',10')->select();
		
		$totalpage =ceil($count/10);

        $discount = $this->getDistrict();

		foreach($list as $k=>&$v)
		{
			$list[$k]['thumb_img'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$v['thumb_img'];
			$list[$k]['catname'] =M('category')->where("id='".$v['catid']."'")->getField('catname');
            $v['price'] = round($v['price']*$discount,2);
			unset($list[$k]['catid']);
		}
		$this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$list,'totalpage'=>$totalpage));
	}

	// 商品列表接口
	public function goodslist()
	{
		$forget = I('get.');

		$page = $forget['page'] ? $forget['page']: '1';
		$keyword = $forget['keyword'];
		$sort   = $forget['sortprice'];  // 1 价格从高到低   2 价格从低到高
		$time   = $forget['sorttime'];  // 1 时间由近到远   2 时间由远到近
		$sales   = $forget['sortsales'];  // 1 时间由近到远   2 时间由远到近
		$catid  = $forget['catid'];
		$where ="issale=1";

        $discount = $this->getDistrict();

		if($catid > 0)
		{
			$where .=" and c.id='$catid'";
		}

		if($keyword)
		{
			$where .= " and a.goodsname like '%$keyword%'";
		}

		if(!empty($sort))
		{
			if($sort ==1)
			{
				$order = "a.price desc";
			}
			if($sort ==2)
			{
				$order = "a.price asc";
			}
		}

        if(!empty($time))
        {
            if($time ==1)
            {
                $order = "a.createtime desc";
            }
            if($time ==2)
            {
                $order = "a.createtime asc";
            }
        }

        if(!empty($sales))
        {
            if($sales ==1)
            {
                $order = "a.number desc";
            }
            if($sales ==2)
            {
                $order = "a.number asc";
            }
        }

		$count =M('goods a')
            ->join(C('DB_PREFIX').'goods_cat b on a.id = b.goods_id')
            ->join(C('DB_PREFIX').'category c on c.id = b.cat_id')
            ->field('a.id,a.goodsname,a.thumb_img,a.price,c.catname')
            ->group('a.id')
            ->where($where)
            ->order($order)
            ->count();

    	$list =M('goods a')
            ->join(C('DB_PREFIX').'goods_cat b on a.id = b.goods_id')
            ->join(C('DB_PREFIX').'category c on c.id = b.cat_id')
            ->field('a.*,c.catname')
            ->group('a.id')
            ->where($where)
            ->order($order)
            ->page($page.',10')
            ->select();

		$totalpage =ceil($count/10);
		foreach($list as $k=>&$v)
		{
			$list[$k]['catname'] =M('category')->where("id='".$v['catid']."'")->getField('catname');
			$list[$k]['thumb_img'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$v['thumb_img'];
			$v['price'] = round($v['price']*$discount,2);
			unset($list[$k]['catid']);
		}
		$this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$list,'totalpage'=>$totalpage));
	}
	
	// 商品详情接口
	public function goods_detail()
	{
		$forget = I('get.');

    	//商品详情
    	$list =M('goods')->field('id,catid,goodsname,price,unit,goodsbrief,goodsdesc,imgs,number,clickcount')->where("issale=1 and id='".$forget['id']."'")->find();
        M('goods')->where(['id'=>$forget['id']])->save(['clickcount'=>$list['clickcount'] + 1]);
        //商品分类
    	$cate = M('goods_spec')->where(['goods_id'=>$forget['id']])->select();
    	//分类规格
        $specification = M('goods_spec_item')->where(['goods_id'=>$forget['id']])->select();
        //规格搭配价格
        $option = M('goods_option')->where(['goods_id'=>$forget['id']])->select();

        //获取用户折扣
        $discount = $this->getDistrict();
        $list['discount'] = $discount;
        $collect = M('collect')->where(['goods_id'=>$forget['id'],'userid'=>$forget['uid']])->find();

        //判断该商品用户是否收藏
        if ($collect) {
            $list['is_collect'] = 1;
        }else{
            $list['is_collect'] = 0;
        }

        $arr_price = array_column($option,'sale_price');
        $min = array_search(min($arr_price), $arr_price);
        $max = array_search(max($arr_price), $arr_price);
        //商品最低价
        $list['min_price'] = round($arr_price[$min]*$discount,2);
        //商品最高价
        $list['max_price'] = round($arr_price[$max]*$discount,2);

        if ($list['min_price'] > 0 || $list['max_price'] > 0) {
            $list['price'] = '';
        }else{
            //折后价格
            $list['price'] = round($list['price']*$discount,2);
        }

        foreach ($cate as &$v)
        {
            foreach ($specification as $v1)
            {
                if ($v['id'] == $v1['specid']) {
                    $v['specification'][] = $v1;
                }
            }
        }

        //拼装图片路径
		if($list)
		{
			$cates = M('category a')
                ->join(C('DB_PREFIX').'goods_cat b on a.id = b.cat_id')
                ->where(['b.goods_id'=>$forget['id']])
                ->field('catname')
                ->select();

            $list['catename'] = implode(',',array_column($cates,'catname'));

			if($list['imgs'])
			{
				$list['imgs'] =json_decode($list['imgs'],true);
				if($list['imgs'])
				{
					foreach($list['imgs']['photo'] as $k=>&$v)
					{
						if($v['url'])
						{
							$list['imgs']['photo'][$k]['url'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$v['url'];
						}
						unset($list['imgs']['photo'][$k]['alt']);
					}
				}
			}
			unset($list['catid']);

            $arr['goods_info'] = $list;
            $arr['cate'] = $cate;
            $arr['option'] = $option;
			$this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$arr));
		}else{
			$this->ajaxReturn(array('msg'=>'商品已下架','status'=>'202','data'=>null));
		}
	}

	//添加收藏、取消收藏
	public function collect()
    {
        $forget = I('post.');
        if ($forget) {
            $data = M('collect')->where(['userid'=>$forget['uid'],'goods_id'=>$forget['id']])->find();
            if ($data) {
                $rst = M('collect')->delete($data['id']);
                $status = 0;
            }else{
                $rst = M('collect')->add(['userid'=>$forget['uid'],'goods_id'=>$forget['id'],'createtime'=>time()]);
                $status = 1;
            }

            if ($rst) {
                $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$status));
            }else{
                $this->ajaxReturn(array('msg'=>'操作失败','status'=>'203','data'=>''));
            }
        }
        $this->ajaxReturn(array('msg'=>'提交方式错误','status'=>'207','data'=>null));
    }

    //加入购物车
    public function add_cart()
    {
        $forget = I('post.');
        $goods = M('goods')->find($forget['goods_id']);
        $discount = $this->getDistrict();
        if (!empty($forget['goods_attr_id'])) {
            $attr = M('goods_option')->where(['id'=>$forget['goods_attr_id']])->find();
            $forget['goods_price'] = $attr['sale_price'] * $discount * $forget['goods_number'];
            $forget['goods_name'] = $goods['goodsname'];
            $forget['goods_attr'] = $attr['title'];
        }else{
            $forget['goods_price'] = $goods['price'] * $discount * $forget['goods_number'];
            $forget['goods_name'] = $goods['goodsname'];
            $forget['goods_attr'] = $goods['unit'];
        }

        $cartModel = M('cart');
        $cart = $cartModel->where(['goods_id'=>$forget['goods_id'],'user_id'=>$forget['uid'],'goods_attr_id'=>$forget['goods_attr_id']])->find();
        if ($cart) {
            //如果购物车中已存在此商品，则累加数量和价格
            $goods_number = $cart['goods_number']+$forget['goods_number'];
            $goods_price = $cart['goods_price']+$forget['goods_price'];
            $rst = $cartModel->where(['id'=>$cart['id']])->save(['goods_number'=>$goods_number,'goods_price'=>$goods_price]);
        }else{
            $forget['user_id'] = $forget['uid'];
            $rst = $cartModel->add($forget);
        }

        if ($rst) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>''));
        }else{
            $this->ajaxReturn(array('msg'=>'操作失败','status'=>'203','data'=>''));
        }
    }

    //购物车列表
    public function cart()
    {
        $forget = I('get.');
        $where['a.user_id'] = $forget['uid'];
        $page = $forget['page'] ? $forget['page']: '1';
        $pageSize = 10;
        $count = M('cart a')
            ->join(C('DB_PREFIX').'goods b on a.goods_id = b.id')
            ->where($where)
            ->count();

        $data = M('cart a')
            ->join(C('DB_PREFIX').'goods b on a.goods_id = b.id')
            ->field('a.*,b.thumb_img')
            ->where($where)
            ->page($page,$pageSize)
            ->select();

        foreach ($data as &$v)
        {
            $v['thumb_img'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$v['thumb_img'];
            $v['goods_price'] = $v['goods_price'] / $v['goods_number'];
        }

        $totalpage =ceil($count/$pageSize);
        $amount = array_sum(array_column($data,'goods_price'));

        if ($data) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$data,'totalpage'=>$totalpage,'amount'=>$amount));
        }else{
            $this->ajaxReturn(array('msg'=>'购物车中暂无商品','status'=>'202','data'=>''));
        }
    }

    //编辑购物车商品
    public function edit_cart()
    {
        $forget = I('post.');
        $cart = M('cart')->where(['rec_id'=>$forget['rec_id'],'user_id'=>$forget['uid']])->find();
        $price = $cart['goods_price'] / $cart['goods_number'] * $forget['goods_number'];
        $rst = M('cart')->where(['rec_id'=>$cart['rec_id']])->save(['goods_number'=>$forget['goods_number'],'goods_price'=>$price]);

        if ($rst) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>''));
        }else{
            $this->ajaxReturn(array('msg'=>'编辑购物车商品失败','status'=>'203','data'=>''));
        }
    }

    //删除购物车商品
    public function delete_cart()
    {
        $forget = I('post.');
        $arr = explode(',',$forget['rec_id']);
        $rst = M('cart')->where(['user_id'=>$forget['uid'],'rec_id'=>['in',$arr]])->delete();

        if ($rst) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>''));
        }else{
            $this->ajaxReturn(array('msg'=>'移除购物车商品失败','status'=>'203','data'=>''));
        }
    }

    //收货地址
    public function shipping_address()
    {
        $forget = I('get.');
        $addressModel = new UserAddressModel();
        $data = $addressModel->getAddress($forget['uid']);
        foreach ($data as &$v)
        {
            if ($v['isdefault'] == 1) {
                $v['isdefault'] = true;
            }else{
                $v['isdefault'] = false;
            }
        }

        if ($data) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$data));
        }else{
            $this->ajaxReturn(array('msg'=>'暂无收货地址','status'=>'202','data'=>null));
        }
    }

    //获取收货地址
    public function get_address()
    {
        $forget = I('get.');
        $addressModel = new UserAddressModel();
        $data = $addressModel->where(['id'=>$forget['id'],'user_id'=>$forget['uid']])->find();

        if ($data) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$data));
        }else{
            $this->ajaxReturn(array('msg'=>'暂无收货地址','status'=>'202','data'=>null));
        }

    }

    //添加收货地址
    public function add_address()
    {
        $forget = I('post.');
        $addressModel = new UserAddressModel();
        $rst = $addressModel->addAddress($forget);
        if ($forget['isdefault'] == 1) {
            $addressModel->where(['id'=>['neq',$rst]])->save(['isdefault'=>0]);
        }

        if ($rst) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>''));
        }else{
            $this->ajaxReturn(array('msg'=>'添加收货地址失败','status'=>'203','data'=>''));
        }
    }

    //编辑收货地址
    public function edit_address()
    {
        $forget = I('post.');
        $addressModel = new UserAddressModel();
        $rst = $addressModel->editAddress($forget);

        if ($rst) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>''));
        }else{
            $this->ajaxReturn(array('msg'=>'编辑收货地址失败','status'=>'203','data'=>''));
        }
    }

    //删除收货地址
    public function delete_address()
    {
        $forget = I('post.');
        $addressModel = new UserAddressModel();
        $rst = $addressModel->where(['id'=>$forget['id'],'user_id'=>$forget['uid']])->delete();

        if ($rst) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>''));
        }else{
            $this->ajaxReturn(array('msg'=>'删除收货地址失败','status'=>'203','data'=>''));
        }
    }

    //获取默认地址
    public function default_address()
    {
        $forget = I('get.');
        $addressModel = new UserAddressModel();
        $address = $addressModel->where(['user_id'=>$forget['uid'],'isdefault'=>1])->find();

        if ($address) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$address));
        }else{
            $this->ajaxReturn(array('msg'=>'暂无默认收货地址','status'=>'202','data'=>''));
        }
    }

    //获取商品信息
    public function goods_info()
    {
        $forget = I('get.');

        switch ($forget['type'])
        {
            case 1:
                $ids = explode(',',$forget['ids']);
                $goods = M('cart')->where(['rec_id'=>['in',$ids]])->select();
                foreach ($goods as &$v)
                {
                    $info = M('goods')->where(['id'=>$v['goods_id']])->find();
                    $v['thumb_img'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$info['thumb_img'];
                    $v['goodsbrief'] = $v['goods_attr'];
                    $v['price'] = $v['goods_price']/$v['goods_number'];
                    $v['goodsname'] = $v['goods_name'];
                }

                $amount = array_sum(array_column($goods,'goods_price'));
                $number = array_sum(array_column($goods,'goods_number'));
                break;
            default:
                $goods = M('goods')->field('id goods_id,goodsname,price,thumb_img,goodsbrief,imgs,goodsdesc')->where(['id'=>$forget['goods_id']])->select();

                //获取用户折扣
                $discount = $this->getDistrict();
                $goods[0]['goods_number'] = $forget['goods_number'];
                $goods[0]['thumb_img'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$goods[0]['thumb_img'];

                //商品所选规格信息
                $option = M('goods_option')->where(['id'=>$forget['option_id']])->find();
                if ($option) {
                    $goods[0]['goodsbrief'] = $option['title'];
                    $goods[0]['price'] = round($option['sale_price']*$discount,2);
                    $goods[0]['goods_attr_id'] = $option['id'];
                }else{
                    $goods[0]['price'] = round($goods[0]['price']*$discount,2);
                }

                $amount = $goods[0]['price'] * $goods[0]['goods_number'];
                $number = $goods[0]['goods_number'];
                $goods[0]['rec_id'] = '';
                break;
        }

        $addressModel = new UserAddressModel();
        $address = $addressModel->where(['user_id'=>$forget['uid'],'isdefault'=>1])->find();

        //会员等级折扣
        $level_discount = $this->getLevelDistrict();
        $discount_amount = $amount * $level_discount;

        if ($goods) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$goods,'discount_amount'=>$discount_amount,'amount'=>$amount,'level_discount'=>$level_discount,'number'=>$number,'address'=>$address));
        }else{
            $this->ajaxReturn(array('msg'=>'暂无商品信息','status'=>'203','data'=>''));
        }
    }

    //创建支付订单
    public function create_order()
    {
        $forget = I('post.');
        $goods = json_decode(htmlspecialchars_decode($forget['goods']),true);
        $orderModel = new OrderModel();
        $orderGoodsModel = new OrderGoodsModel();
        $cartModel = new CartModel();
        //获取会员等级折扣
        $level_discount = $this->getLevelDistrict();

        $forget['orderno'] = sp_get_order_sn();

        $amount = 0;
        foreach ($goods as &$v)
        {
            $amount += $v['price'] * $v['goods_number'];
        }
        unset($v);
        $forget['amount'] = $amount;
        $forget['paymoney'] = $amount * $level_discount;
        $order_id = $orderModel->add_order($forget);

        if ($order_id) {
            //判断是否是购物车中商品
            foreach ($goods as $v)
            {
                $v['order_id'] = $order_id;
                //原价= 现价/折扣比例
                $v['current_price'] = round($v['price'] * $level_discount,2);
                $v['number'] = $v['goods_number'];
                $v['total_price'] = $v['number'] * $v['current_price'];
                $v['specification_id'] = $v['goods_attr_id'];
                $rst = $orderGoodsModel->addGoods($v);
                if ($rst == false) {
                    $orderModel->delete_order($order_id);
                    $orderGoodsModel->where(['order_id'=>$order_id])->delete();
                    $this->ajaxReturn(array('msg'=>'创建订单失败','status'=>'203','data'=>''));
                }

                if ($forget['iscart']==1) {
                    //移除购物车中商品
                    $cartModel->where(['user_id' => $forget['uid'], 'rec_id' => $v['rec_id']])->delete();
                }
            }

            M('pay_log')->add(['ordersn'=>$forget['orderno'],'orderamount'=>$forget['paymoney'],'ordertype'=>1,'ispaid'=>0]);

            $data['order_id'] = $order_id;
            $data['order_no'] = $forget['orderno'];
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$data));
        }else{
            $this->ajaxReturn(array('msg'=>'创建订单失败','status'=>'203','data'=>''));
        }
    }

    //收银台
    public function cashier_order()
    {
        $forget = I('get.');
        $data = M('order')->where(['id'=>$forget['id'],'uid'=>$forget['uid']])->find();
        $member = M('member')->where(['id'=>$forget['uid']])->find();
        $data['credit_day'] = $member['credit_day'] - floor((time() - strtotime(date("Y-m-d",$member['credit_time'])))/(60*60*24));
        if ($data['credit_day'] < 0) {
            $data['credit_day'] = 0;
        }
        $data['balance'] = $member['balance'];

        if ($data) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$data));
        }else{
            $this->ajaxReturn(array('msg'=>'订单号不存在','status'=>'202','data'=>''));
        }
    }

    //支付
    public function pay()
    {
        $forget = I('post.');
        $userInfo = D('member')->where(['id'=>$forget['uid']])->find();
        if ($userInfo) {
            $orderModel = new OrderModel();
            $order = $orderModel->find($forget['order_id']);
            switch ($forget['pay_type'])
            {
                case 1://微信
                    break;
                case 2://支付宝
                    break;
                case 3://余额支付
                    if ($userInfo['balance'] >= $order['paymoney']) {
                        $balance = $userInfo['balance'] - $order['paymoney'];
                        M('member')->where(['id'=>$forget['uid']])->save(['balance'=>$balance]);
                        $where['id'] = $forget['order_id'];
                        $where['member_id'] = $forget['uid'];
                        $condition['paytype'] = $forget['pay_type'];
                        $condition['status'] = 3;
                        $condition['pay_time'] = time();
                        $orderModel->edit_order($where,$condition);
                        //提升会员等级
                        $this->improveLevel($forget['uid'],$order['paymoney']);
                        //增加商品销售量、减少库存
                        $this->increaseGoodsSales($order['id']);
                    }else{
                        $this->ajaxReturn(array('msg'=>'余额不足','status'=>'206','data'=>''));
                    }
                    break;
                case 4://赊账
                    $available_day =$userInfo['credit_day'] - floor((time() - $userInfo['credit_time'])/(60*60*24));
                    if ($available_day>=0) {
                        $where['id'] = $forget['order_id'];
                        $where['member_id'] = $forget['uid'];
                        $condition['paytype'] = $forget['pay_type'];
                        $condition['status'] = 3;
                        $condition['pay_time'] = time();
                        $orderModel->edit_order($where,$condition);
                        //提升会员等级
                        $this->improveLevel($forget['uid'],$order['paymoney']);
                        //增加商品销售量
                        $this->increaseGoodsSales($order['id']);
                    }else{
                        $this->ajaxReturn(array('msg'=>'没有可用赊账时间','status'=>'206','data'=>''));
                    }
                    break;
                default:
                    $this->ajaxReturn(array('msg'=>'错误的支付方式','status'=>'203','data'=>''));
                    break;
            }

            M('pay_log')->where(['ordersn'=>$order['orderno']])->save(['ispaid'=>1]);
            $this->ajaxReturn(array('msg'=>'支付成功','status'=>'200','data'=>''));
        }

        $this->ajaxReturn(array('msg'=>'信息错误','status'=>'202','data'=>''));
    }

    //获取全部订单
    public function get_orders()
    {
        $forget = I('get.');
        $orderModel = new OrderModel();
        $orderGoodsModel = new OrderGoodsModel();
        $where['member_id'] = $forget['uid'];
        if ($forget['status']) {
            $where['status'] = $forget['status'];
        }
        $data = $orderModel->where($where)->order('id desc')->select();
        foreach ($data as &$v)
        {
            $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $v['paytype'] = $this->getPayType($v['paytype']);
            $v['goods'] = $orderGoodsModel->where(['order_id'=>$v['id']])->select();
            if ($v['goods']) {
                foreach ($v['goods'] as &$m)
                {
                    $m['amount'] = $m['current_price'] * $m['number'];
                }
                $v['count'] = array_sum(array_column($v['goods'],'number'));
            }else{
                unset($v);
            }

        }

        if ($data) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$data));
        }else{
            $this->ajaxReturn(array('msg'=>'暂无订单数据','status'=>'202','data'=>''));
        }
    }

    //订单详情
    public function order_detail()
    {
        $forget = I('get.');
        $orderModel = new OrderModel();
        $orderGoodsModel = new OrderGoodsModel();
        $data = $orderModel->find($forget['order_id']);
        $data['create_time'] = date('Y-m-d H:i:s',$data['create_time']);

        if ($data['pay_time']) {
            $data['pay_time'] = date('Y-m-d H:i:s',$data['pay_time']);
        }

        if ($data['send_time']) {
            $data['send_time'] = date('Y-m-d H:i:s',$data['send_time']);
        }

        if ($data['receive_time']) {
            $data['receive_time'] = date('Y-m-d H:i:s',$data['receive_time']);
        }

        $data['paytype'] = $this->getPayType($data['paytype']);
        $data['goods'] = $orderGoodsModel->where(['order_id'=>$forget['order_id']])->select();
        $data['count'] = array_sum(array_column($data['goods'],'number'));

        if ($data) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$data));
        }else{
            $this->ajaxReturn(array('msg'=>'订单信息不存在','status'=>'202','data'=>''));
        }
    }

    //赊账协议
    public function credit_agreement()
    {
        $data = M('options')->where(['option_name'=>'xieyi_options'])->find();
        $data['option_value'] = json_decode($data['option_value'],true);
        if ($data) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$data));
        }else{
            $this->ajaxReturn(array('msg'=>'协议信息不存在','status'=>'202','data'=>''));
        }
    }

    //会员等级说明
    public function level_description()
    {
        $forget = I('get.');
        $data = M('options')->where(['option_name'=>'level_options'])->find();
        $data['option_value'] = json_decode($data['option_value'],true);
        $levels = M('member_level')->order('maxprice asc')->select();
        $member = M('member')->find($forget['uid']);

        $lv = 0;
        foreach ($levels as $k=>$v)
        {
            if ($v['id'] == $member['level_id']) {
                $lv = $k;
            }
        }

        if ($data) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$data,'levels'=>$levels,'member_lv'=>$lv));
        }else{
            $this->ajaxReturn(array('msg'=>'暂无会员等级信息','status'=>'202','data'=>''));
        }
    }

    //确认收货
    public function confirm_receipt()
    {
        $forget = I('get.');
        $rst = M('order')->where(['id'=>$forget['order_id'],'member_id'=>$forget['uid']])->save(['status'=>1]);

        if ($rst) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>''));
        }else{
            $this->ajaxReturn(array('msg'=>'确认收货失败','status'=>'202','data'=>''));
        }
    }

    //获取用户余额
    public function get_balance()
    {
        $forget = I('get.');
        $balance = M('member')->where(['id'=>$forget['uid']])->getField('balance');

        if ($balance) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$balance));
        }else{
            $this->ajaxReturn(array('msg'=>'暂无用户信息','status'=>'202','data'=>''));
        }
    }

    //充值订单
    public function recharge_order()
    {
        $forget = I('post.');
        $config = M('recharge_config')->find();
        if ($config) {
            if ($forget['amount'] <= $config['below']) {
                $config['below_rate']>0?$config['below_rate']:$config['below_rate']=1;
                $forget['account_money'] = $forget['amount'] + $forget['amount']*$config['below_rate'];
            }

            if ($forget['amount'] > $config['middle_below'] && $forget['amount'] <= $config['middle_above']) {
                $config['middle_rate']>0?$config['middle_rate']:$config['middle_rate']=1;
                $forget['account_money'] = $forget['amount'] + $forget['amount']*$config['middle_rate'];
            }

            if ($forget['amount'] > $config['above']) {
                $config['above_rate']>0?$config['above_rate']:$config['above_rate']=1;
                $forget['account_money'] = $forget['amount'] + $forget['amount']*$config['above_rate'];
            }
        }

        $forget['recharge_amount'] = $forget['amount'];
        $order_no = sp_get_order_sn();

        $forget['member_id'] = $forget['uid'];
        $forget['recharge_no'] = $order_no;
        $forget['atype'] = 1;
        $forget['create_time'] = time();
        $data['order_id'] = M('recharge')->add($forget);
        $data['order_no'] = $order_no;

        M('pay_log')->add(['ordersn'=>$order_no,'orderamount'=>$forget['amount'],'ordertype'=>2,'ispaid'=>0]);
        if ($data) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$data));
        }else{
            $this->ajaxReturn(array('msg'=>'暂无充值订单','status'=>'202','data'=>''));
        }
    }

    //充值记录
    public function recharge_record()
    {
        $forget = I('get.');
        $forget['page']?$forget['page']:$forget['page'] = 1;
        $where['member_id'] = $forget['uid'];
        $where['atype'] = 1;
        $where['status'] = 1;
        $count = M('recharge')->where($where)->count();
        $pageSize = 10;
        $total_page = ceil($count/$pageSize);
        $data = M('recharge')->where($where)->page($forget['page'],$pageSize)->order('id desc')->select();

        foreach ($data as &$v)
        {
            $v['paytime'] = date('Y-m-d H:i:s',$v['paytime']);
        }

        if ($data) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$data,'total_page'=>$total_page));
        }else{
            $this->ajaxReturn(array('msg'=>'暂无充值记录','status'=>'202','data'=>''));
        }
    }

    //充值订单详情
    public function recharge_detail()
    {
        $forget = I('get.');
        $data = M('recharge')->where(['recharge_no'=>$forget['order_no'],'member_id'=>$forget['uid']])->find();

        if ($data) {
            $this->ajaxReturn(array('msg'=>'正常返回','status'=>'200','data'=>$data));
        }else{
            $this->ajaxReturn(array('msg'=>'暂无充值记录','status'=>'202','data'=>''));
        }
    }
}