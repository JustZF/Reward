<?php
class Reward {
    protected $Count;            //个数
    protected $Money;            //总金额(分)
    protected $RemainCount;      //剩余个数
    protected $RemainMoney;      //剩余金额(分)
    protected $BestMoney;        //手气最佳金额
    protected $BestMoneyIndex;   //手气最佳序号
    protected $MoneyList;        //拆分列表
    
    public function __construct(int $count, $money){
        $this->Count       = $count;
        $this->Money       = $money * 100;
        $this->RemainCount = $count;
        $this->RemainMoney = $money * 100;
        $this->BestMoney   = 0;
    }
    
    //红包算法
    protected function GrabReward() {
        
        if($this->RemainCount <= 0) {
            return false;
        }

        //最后一个
        if($this->RemainCount - 1 == 0) {
            $money = $this->RemainMoney;
            $this->RemainCount = 0;
		    $this->RemainMoney = 0;
            return $money;
        }

        //是否可以直接0.01
        if (($this->RemainMoney / $this->RemainCount) == 1 ){
            $this->RemainMoney -= 1;
            $this->RemainCount--;
            return 1;
        }

        //最大可领金额 = 剩余金额的平均值x2 = (剩余金额 / 剩余数量) * 2
        $maxMoney = ($this->RemainMoney / $this->RemainCount) * 2;
        $money = rand(1, $maxMoney);
        $this->RemainMoney -= $money;
        //防止剩余金额负数
        if ($this->RemainMoney < 0) {
            $money += $this->RemainMoney;
            $this->RemainMoney = 0;
            $this->RemainCount = 0;
        } else {
            $this->RemainCount--;
        }

        return $money;
    }

    //生成拆分队列
    public function SetMoneyList() {
        for($i = 0; $this->RemainCount > 0; $i++) {
            //调动核心算法
            $money = $this->GrabReward();
            //记录最佳
            if($money > $this->BestMoney) {
                $this->BestMoney      = $money;
                $this->BestMoneyIndex = $i;
            }
            $this->MoneyList[] = $money;
        }
    }
    
    //魔术获取
    public function __get($propertyName)
    {   
        return $this->$propertyName;
    }
}

// (new Reward(5, 50))->SetMoneyList()->getParam();

$Reward = new Reward(30, 30);
$Reward->SetMoneyList();


for ($i = 0; $i < count($Reward->MoneyList); $i++) { 
    if($Reward->BestMoneyIndex == $i) {
        echo '第' . ($i+1) . '次红包金额为：￥' . sprintf('%.2f', $Reward->MoneyList[$i] / 100) .'【手气最佳】';
    } else {
        echo '第' . ($i+1) . '次红包金额为：￥' . sprintf('%.2f', $Reward->MoneyList[$i] / 100);
    }
    echo '</br>';
}
// var_dump($Reward->MoneyList);
// var_dump($Reward->BestMoneyIndex);
// var_dump($Reward->BestMoney);

