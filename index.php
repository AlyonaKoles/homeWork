<?php
class Coffee {
    public $name;      
    public $size;      
    protected $price;
    private $recipe;
    
    public function __construct($name, $size = 'средний') {
        $this->name = $name;
        $this->size = $size;
        $this->setPriceBySize();  
        $this->recipe = $this->getDefaultRecipe();  
        
        echo "Заказ готов: {$this->name} ({$this->size})\n";
    }
    
    private function setPriceBySize() {
        if ($this->size == 'маленький') {
            $this->price = 150;
        } elseif ($this->size == 'средний') {
            $this->price = 200;
        } elseif ($this->size == 'большой') {
            $this->price = 250;
        } else {
            $this->price = 200; 
        }
    }
    
    private function getDefaultRecipe() {
        if ($this->name == 'latte') {
            return 'Эспрессо + молоко + пенка';
        } elseif ($this->name == 'cappuccino') {
            return 'Эспрессо + молоко + много пенки';
        } elseif ($this->name == 'americano') {
            return 'Эспрессо + вода';
        } else {
            return 'Эспрессо';
        }
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    protected function setPrice($newPrice) {
        if ($newPrice > 0) {
            $this->price = $newPrice;
        }
    }
    
    public function brew() {
        echo "Готовится: {$this->name}\n";
        echo "Рецепт: {$this->recipe}\n";
        echo "Цена: {$this->price} руб.\n";
    }
    
    public function getInfo() {
        return "Кофе: {$this->name}, Размер: {$this->size}, Цена: {$this->price} руб.";
    }
}


interface OrderInterface {
    public function addToOrder();    
    public function pay();           
    public function getReceipt();    
}


trait AdditivesTrait {

    protected $additives = [];
    protected $additivePrices = [
        'сахар' => 10,
        'сироп' => 30,
        'сливки' => 25
    ];
    
    public function addAdditive($name) {
        if (isset($this->additivePrices[$name])) {
            $this->additives[] = $name;
            $oldPrice = $this->getPrice();
            $this->setPrice($oldPrice + $this->additivePrices[$name]);
            echo "Добавка: {$name}\n";
            return true;
        } else {
            echo "Нет такой добавки: {$name}\n";
            return false;
        }
    }

    public function getAdditivesList() {
        if (empty($this->additives)) {
            return "без добавок";
        }
        return implode(', ', $this->additives);
    }
}

class OrderItem extends Coffee implements OrderInterface {
    use AdditivesTrait;
    
    private $customer;     
    private $quantity = 1; 
    private $isPaid = false; 
    
    public function __construct($name, $size, $customer) {
        parent::__construct($name, $size); 
        $this->customer = $customer;
        echo "Заказчик: {$customer}\n";
    }
    
    public function setQuantity($qty) {
        if ($qty > 0 && $qty <= 5) {
            $this->quantity = $qty;
            echo "Количество: {$qty}\n";
        } else {
            echo "Количество должно быть от 1 до 5\n";
        }
    }
    
    public function addToOrder() {
        echo "Добавлен заказ:\n";
        echo "{$this->name} x{$this->quantity}\n";
        echo "Добавки: " . $this->getAdditivesList() . "\n";
    }
    
    public function pay() {
        if ($this->isPaid) {
            echo "Заказ уже оплачен.\n";
            return false;
        }
        
        $total = $this->getPrice() * $this->quantity;
        echo "Оплата: {$total} руб.\n";
        
        $this->isPaid = true;
        echo "Оплачено.\n";
        
        $this->brew();
        return true;
    }
    
    public function getReceipt() {
        $total = $this->getPrice() * $this->quantity;
        
        $receipt = "\nЧЕК\n";
        $receipt .= "Клиент: {$this->customer}\n";
        $receipt .= "{$this->name} x{$this->quantity}\n";
        
        if (!empty($this->additives)) {
            $receipt .= "Добавки: " . $this->getAdditivesList() . "\n";
        }
        
        $receipt .= "Сумма: {$total} руб.\n";
        $receipt .= "Статус: " . ($this->isPaid ? "Оплачено" : "Не оплачено") . "\n";
        
        return $receipt;
    }
    
    public function getInfo() {
        return parent::getInfo() . ", Клиент: {$this->customer}, Количество: {$this->quantity}";
    }
}

$order = new OrderItem('latte', 'большой', 'Иван');

echo "\n";
$order->addToOrder();
$order->addAdditive('сироп');
$order->addAdditive('сахар');
$order->setQuantity(2);
echo "\nОбщая информация:\n";
echo $order->getInfo() ."\n";
echo "\n";
$order->pay();
echo $order->getReceipt();

echo "\n";
$order2 = new OrderItem('cappuccino', 'средний', 'Алёна');
$order2->addAdditive('сливки');
echo "\nОбщая информация:\n";
echo $order2->getInfo() ."\n";
echo "\n";
$order2->pay();
echo $order2->getReceipt();
?>