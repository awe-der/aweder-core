<?php

use App\Category;
use App\Contract\Repositories\OrderItemContract;
use App\Inventory;
use App\InventoryOptionGroup;
use App\InventoryOptionGroupItem;
use App\Merchant;
use App\MerchantPayment;
use App\Order;
use App\OrderItem;
use App\Provider;
use App\User;
use Illuminate\Database\Seeder;

class JumpTwentyFourMerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!User::where('email', 'test@test.com')->first()) {
            $user = factory(User::class)->create([
                'email' => 'test@test.com',
                'password' => Hash::make('password')
            ]);

            $merchant = factory(Merchant::class)->create(
                [
                    'url_slug' => 'awe-der-store',
                    'name' => 'Awe-der Store',
                    'contact_email' => 'test@test.com',
                    'contact_number' => '0121 296 9999',
                    'address' => '34 Fleet Street, London, W1T 4PT',
                    'allow_collection' => 1,
                    'allow_delivery' => 1,
                    'delivery_radius' => 6,
                    'delivery_cost' => 599,
                    'description' => 'Welcome to the Awe-der test store',
                ]
            );

            $user->merchants()->attach($merchant->id);

            $categoryOne = factory(Category::class)->create(
                [
                    'merchant_id' => $merchant->id,
                    'order' => 1,
                    'title' => 'Starters',
                ]
            );

            $categoryTwo = factory(Category::class)->create(
                [
                    'merchant_id' => $merchant->id,
                    'order' => 2,
                    'title' => 'Mains',
                ]
            );

            $categoryThree = factory(Category::class)->create(
                [
                    'merchant_id' => $merchant->id,
                    'order' => 3,
                    'title' => 'Sides',
                ]
            );

            $categoryFour = factory(Category::class)->create(
                [
                    'merchant_id' => $merchant->id,
                    'order' => 4,
                    'title' => 'Desserts',
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryOne->id,
                    'title' => 'Nachos & Cheese',
                    'description' => 'A lovely big serving of Nachos & Cheese',
                    'price' => 500,
                    'available' => 1,
                ]
            );

            $startersInventory = factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryOne->id,
                    'title' => 'Nachos, Cheese & Chilli',
                    'description' => 'A lovely big serving of Nachos & Cheese with some spicy Chilli',
                    'price' => 500,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryOne->id,
                    'title' => 'Sweet potato fries',
                    'description' => '',
                    'price' => 600,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryOne->id,
                    'title' => 'Spicy Chicken Wings',
                    'description' => 'Spicy Spicy Spicy',
                    'price' => 600,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryOne->id,
                    'title' => 'Chilli cheese fries',
                    'description' => 'Skin-on fries topped with a rich chunky
                    beef & black bean beef chilli, red onion, jalapeños & cheese.',
                    'price' => 500,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryTwo->id,
                    'title' => 'Beef Burger',
                    'description' => '100% prime short rib and chuck beef patty served
                    in a toasted brioche bun smothered in a creamy tomato and gherkin sauce.
                    With caramelised onions, beef tomato and baby gem.',
                    'price' => 800,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryTwo->id,
                    'title' => 'Veggie Burger',
                    'description' => '',
                    'price' => 800,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryTwo->id,
                    'title' => 'Chicken Burger',
                    'description' => 'Butter milk Chicken Burger',
                    'price' => 900,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryTwo->id,
                    'title' => 'Chilli con Carne',
                    'description' => '',
                    'price' => 800,
                    'available' => 1,
                ]
            );

            $mainsInventory = factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryTwo->id,
                    'title' => 'Veggie Chilli',
                    'description' => '',
                    'price' => 800,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryThree->id,
                    'title' => 'Chicken Wings',
                    'description' => '',
                    'price' => 800,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryThree->id,
                    'title' => 'Sweet Potato Fries',
                    'description' => '',
                    'price' => 800,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryThree->id,
                    'title' => 'Curly Fries',
                    'description' => '',
                    'price' => 800,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryThree->id,
                    'title' => 'Garlic Bread',
                    'description' => '',
                    'price' => 500,
                    'available' => 1,
                ]
            );

            $sidesInventory = factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryThree->id,
                    'title' => 'Chilli cheese fries',
                    'description' => '',
                    'price' => 700,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryFour->id,
                    'title' => 'Chocolate Cake',
                    'description' => 'Served with either vanilla ice cream or custard',
                    'price' => 400,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryFour->id,
                    'title' => 'Apple Pie',
                    'description' => 'Served with either vanilla ice cream or custard',
                    'price' => 400,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryFour->id,
                    'title' => 'Key Lime Pie',
                    'description' => '',
                    'price' => 400,
                    'available' => 1,
                ]
            );

            factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryFour->id,
                    'title' => '3 Scoops of Ice Cream',
                    'description' => 'Chocolate, Vanilla or Strawberry ice cream',
                    'price' => 400,
                    'available' => 1,
                ]
            );

            $dessertInventory = factory(Inventory::class)->state('variants')->create(
                [
                    'merchant_id' => $merchant->id,
                    'category_id' => $categoryFour->id,
                    'title' => 'Sticky Toffee Pudding',
                    'description' => '',
                    'price' => 400,
                    'available' => 1,
                ]
            );


            // inventory options seeding
            $inventoryOptionGroup = factory(InventoryOptionGroup::class)->create(
                [
                    'name' => 'Extras',
                    'title' => 'Modify your order',
                    'inventory_id' => $mainsInventory->id
                ]
            );

            $inventoryOptionGroupItem1 = factory(InventoryOptionGroupItem::class)->create(
                [
                    'name' => 'Extra chips',
                    'price_modified' => 150,
                    'inventory_option_group_id' => $inventoryOptionGroup->id
                ]
            );

            $inventoryOptionGroupItem2 = factory(InventoryOptionGroupItem::class)->create(
                [
                    'name' => 'Side salad',
                    'price_modified' => 400,
                    'inventory_option_group_id' => $inventoryOptionGroup->id
                ]
            );

            $inventoryOptionGroupItem3 = factory(InventoryOptionGroupItem::class)->create(
                [
                    'name' => 'Upgrade to large',
                    'price_modified' => 290,
                    'inventory_option_group_id' => $inventoryOptionGroup->id
                ]
            );

            for ($i = 0; $i < 5; $i++) {
                $orderOne = factory(Order::class)->state('Incomplete Order')
                    ->create(
                        [
                            'merchant_id' => $merchant->id,
                        ]
                    );

                $orderItemStarter = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $startersInventory->id,
                        'price' => $startersInventory->price,
                    ]
                );

                $orderItemMains = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $mainsInventory->id,
                        'price' => $mainsInventory->price,
                    ]
                );

                $orderItemSides = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $sidesInventory->id,
                        'price' => $sidesInventory->price,
                    ]
                );

                $orderItemDessert = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $dessertInventory->id,
                        'price' => $dessertInventory->price,
                    ]
                );

                $total = $startersInventory->price +
                    $mainsInventory->price +
                    $sidesInventory->price +
                    $dessertInventory->price;

                $orderOne->total_cost = $total;
                $orderOne->save();
            }

            for ($i = 0; $i < 8; $i++) {
                $orderOne = factory(Order::class)->state('Purchased Order')->create([
                    'merchant_id' => $merchant->id,
                    'delivery_cost' => $merchant->delivery_cost,
                ]);

                $orderItemStarter = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $startersInventory->id,
                        'price' => $startersInventory->price,
                    ]
                );

                $orderItemMains = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $mainsInventory->id,
                        'price' => $mainsInventory->price,
                    ]
                );

                $orderItemRepository = app()->make(OrderItemContract::class);

                random_int(0, 1) ? $orderItemRepository->addOptionToOrderItem(
                    $orderItemMains,
                    $inventoryOptionGroupItem1
                ) : null;

                random_int(0, 1) ? $orderItemRepository->addOptionToOrderItem(
                    $orderItemMains,
                    $inventoryOptionGroupItem2
                ) : null;

                random_int(0, 1) ? $orderItemRepository->addOptionToOrderItem(
                    $orderItemMains,
                    $inventoryOptionGroupItem3
                ) : null;

                $orderItemSides = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $sidesInventory->id,
                        'price' => $sidesInventory->price,
                    ]
                );

                $orderItemDessert = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $dessertInventory->id,
                        'price' => $dessertInventory->price,
                    ]
                );

                $total = $startersInventory->price +
                    $mainsInventory->price +
                    $sidesInventory->price +
                    $dessertInventory->price;

                $orderOne->total_cost = $total;
                $orderOne->save();
            }

            for ($i = 0; $i < 3; $i++) {
                $orderOne = factory(Order::class)->state('Payment Rejected')->create([
                    'merchant_id' => $merchant->id,
                ]);

                $orderItemStarter = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $startersInventory->id,
                        'price' => $startersInventory->price,
                    ]
                );

                $orderItemMains = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $mainsInventory->id,
                        'price' => $mainsInventory->price,
                    ]
                );

                $orderItemSides = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $sidesInventory->id,
                        'price' => $sidesInventory->price,
                    ]
                );

                $orderItemDessert = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $dessertInventory->id,
                        'price' => $dessertInventory->price,
                    ]
                );

                $total = $startersInventory->price +
                    $mainsInventory->price +
                    $sidesInventory->price +
                    $dessertInventory->price;

                $orderOne->total_cost = $total;
                $orderOne->save();
            }

            for ($i = 0; $i < 3; $i++) {
                $orderOne = factory(Order::class)->state('Acknowledged Order')->create([
                    'merchant_id' => $merchant->id,
                    'is_delivery' => 1,
                    'delivery_cost' => $merchant->delivery_cost,
                ]);

                $orderItemStarter = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $startersInventory->id,
                        'price' => $startersInventory->price,
                    ]
                );

                $orderItemMains = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $mainsInventory->id,
                        'price' => $mainsInventory->price,
                    ]
                );

                $orderItemSides = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $sidesInventory->id,
                        'price' => $sidesInventory->price,
                    ]
                );

                $orderItemDessert = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $dessertInventory->id,
                        'price' => $dessertInventory->price,
                    ]
                );

                $total = $startersInventory->price +
                    $mainsInventory->price +
                    $sidesInventory->price +
                    $dessertInventory->price;

                $orderOne->total_cost = $total;
                $orderOne->save();
            }

            for ($i = 0; $i < 3; $i++) {
                $orderOne = factory(Order::class)->state('Unacknowledged Order')->create([
                    'merchant_id' => $merchant->id,
                ]);

                $orderItemStarter = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $startersInventory->id,
                        'price' => $startersInventory->price,
                    ]
                );

                $orderItemMains = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $mainsInventory->id,
                        'price' => $mainsInventory->price,
                    ]
                );

                $orderItemSides = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $sidesInventory->id,
                        'price' => $sidesInventory->price,
                    ]
                );

                $orderItemDessert = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $dessertInventory->id,
                        'price' => $dessertInventory->price,
                    ]
                );

                $total = $startersInventory->price +
                    $mainsInventory->price +
                    $sidesInventory->price +
                    $dessertInventory->price;

                $orderOne->total_cost = $total;
                $orderOne->save();
            }

            for ($i = 0; $i < 3; $i++) {
                $orderOne = factory(Order::class)->state('Fulfilled')->create([
                    'merchant_id' => $merchant->id,
                    'is_delivery' => 1,
                    'delivery_cost' => $merchant->delivery_cost,
                ]);

                $orderItemStarter = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $startersInventory->id,
                        'price' => $startersInventory->price,
                    ]
                );

                $orderItemMains = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $mainsInventory->id,
                        'price' => $mainsInventory->price,
                    ]
                );

                $orderItemSides = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $sidesInventory->id,
                        'price' => $sidesInventory->price,
                    ]
                );

                $orderItemDessert = factory(OrderItem::class)->create(
                    [
                        'order_id' => $orderOne->id,
                        'inventory_id' => $dessertInventory->id,
                        'price' => $dessertInventory->price,
                    ]
                );

                $total = $startersInventory->price +
                    $mainsInventory->price +
                    $sidesInventory->price +
                    $dessertInventory->price;

                $orderOne->total_cost = $total;
                $orderOne->save();
            }
        }
    }
}
