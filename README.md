# 使用说明

## 生成 mapping properties

```php
<?php
namespace App\Entities;

use Carbon\Carbon;
use Kabunx\Elastic\Hydrate\EsEntity;
use Kabunx\Elastic\Hydrate\EsProperty;
use Kabunx\Elastic\Exceptions\EsException;

class UserEntity extends EsEntity
{

    #[EsProperty("userId")]
    public int $id;

    public string $name;

    public ?Carbon $date;

    /**
     * @var array|UserAddressEntity[]
     */
     #[EsProperty(UserAddressEntity::class)]
    public $address = [];
}
```

```php
<?php
use App\Entities\UserEntity;

(new UserEntity())->getProperties()
```

结果如下
```shell
---
```
## 如何检索

在orm中，可直接用DB门面类来执行MySQL操作，相当于实例化`Illuminate\Database\Query\Builder`类。

那么ES应该也可以这样操作，所以我也加入了一个`Builder`类，请参考`kabunx/elasticsearch`扩展

当然`Model`模型依赖可以按原来的方式操作，实例了`EsBuilder`，但最终都是通过`Builder`来构造查询参数
```php
(new Builder())
    ->select(['id'])
    ->from('index')
    ->term('is_new', 1)
    ->should(function (Builder $builder) { // bool
        $builder->term('is_new', 1);
        $builder->gte('update_time', 1);
    }, true)
    ->should(function (Builder $builder) {
        $builder
            ->term('function_type', 20057)
            ->should(function (Builder $builder) {
                $builder->like('name', '销售工程师');
                $builder->like('name', '医疗');
            })
            ->should(function (Builder $builder) {
                $builder->like('name', '销售工程师xx');
            });
    })
    ->orderByDesc('id')
    ->limit(2)
    ->toSearchParams();

User::elasticsearch()
    ->term('is_new', 1)
    ->should(function (Builder $builder) { // bool
        $builder->term('is_new', 1);
        $builder->gte('update_time', 1);
    }, true)
    ->should(function (Builder $builder) {
        $builder
            ->term('function_type', 20057)
            ->should(function (Builder $builder) {
                $builder->like('name', '销售工程师');
                $builder->like('name', '医疗');
            })
            ->should(function (Builder $builder) {
                $builder->like('name', '销售工程师xx');
            });
    })
    ->orderByDesc('id')
    ->limit(2)
    ->toSearchParams();
```
最终打印的结果是（这里我仅贴了一个，唯一 的区别就是'index'不同）
```bash
array:3 [
  "index" => "gb_jd"
  "size" => 2
  "body" => array:2 [
    "query" => array:1 [
      "bool" => array:3 [
        "minimum_should_match" => 1
        "must" => array:1 [
          0 => array:1 [
            "term" => array:1 [
              "is_new" => array:1 [
                "value" => 1
              ]
            ]
          ]
        ]
        "should" => array:3 [
          0 => array:1 [
            "term" => array:1 [
              "is_new" => array:1 [
                "value" => 1
              ]
            ]
          ]
          1 => array:1 [
            "range" => array:1 [
              "update_time" => array:1 [
                "gte" => 1
              ]
            ]
          ]
          2 => array:1 [
            "bool" => array:3 [
              "minimum_should_match" => 1
              "must" => array:1 [
                0 => array:1 [
                  "term" => array:1 [
                    "function_type" => array:1 [
                      "value" => 20057
                    ]
                  ]
                ]
              ]
              "should" => array:2 [
                0 => array:1 [
                  "bool" => array:1 [
                    "must" => array:2 [
                      0 => array:1 [
                        "wildcard" => array:1 [
                          "name" => array:1 [
                            "value" => "*销售工程师*"
                          ]
                        ]
                      ]
                      1 => array:1 [
                        "wildcard" => array:1 [
                          "name" => array:1 [
                            "value" => "*医疗*"
                          ]
                        ]
                      ]
                    ]
                  ]
                ]
                1 => array:1 [
                  "wildcard" => array:1 [
                    "name" => array:1 [
                      "value" => "*销售工程师xx*"
                    ]
                  ]
                ]
              ]
            ]
          ]
        ]
      ]
    ]
    "sort" => array:1 [
      0 => array:1 [
        "id" => array:1 [
          "order" => "desc"
        ]
      ]
    ]
  ]
]
```

直接通过ES检索数据

```php
User::elasticsearch()->rawQuery([])
```

获取数据
```php
$esCollection = User::elasticsearch()->get()
```