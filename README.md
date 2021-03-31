# 使用说明

## 生成 Mapping

```php
<?php
namespace App\Entities;


use Golly\Elastic\Hydrate\Entity;
use Golly\Elastic\Hydrate\Annotations\Mapping;
use Golly\Elastic\Exceptions\ElasticException;

/**
 * Class UserEntity
 * @package App\Entities
 */
class UserEntity extends Entity
{

    /**
     * @Mapping(type="long")
     *
     * @var int
     */
    public $id;

    /**
     * @Mapping(type="text", analyzer="ik_smart")
     *
     * @var
     */
    public $name;

    /**
     * @Mapping(type="date", format="Y-m-d")
     *
     * @var string
     */
    public $date;

    /**
     * @Mapping(type="relation")
     *
     * @var array|UserAddressEntity[]
     */
    public $address = [];

    /**
     * @param boolean $relation
     * @return array
     * @throws ElasticException
     */
    public static function mapping($relation = true)
    {
        $mapping = parent::mapping($relation);
        if ($relation) {
            $mapping = array_merge($mapping, [
                'address' => [
                    'properties' => UserAddressEntity::mapping($relation)
                ]
            ]);
        }

        return $mapping;
    }

    /**
     * @param array $data
     * @param boolean $original
     * @return UserEntity
     * @throws ElasticException
     */
    public function toObject(array $data, $original = true)
    {
        $entity = parent::toObject($data, $original);
        if ($entity->address) {
            foreach ($entity->address as &$address) {
                $address = UserAddressEntity::instance($address, $original);
            }
        }

        return $entity;
    }
}
```

```php
<?php
use App\Entities\UserEntity;

UserEntity::mapping();
```

结果如下
```shell
array:4 [
  "id" => array:1 [
    "type" => "long"
  ]
  "name" => array:2 [
    "type" => "text"
    "analyzer" => "ik_smart"
  ]
  "date" => array:2 [
    "type" => "date"
    "format" => "yyyy-MM-dd"
  ]
  "address" => array:1 [
    "properties" => array:1 [
      "city" => array:1 [
        "type" => "keyword"
      ]
    ]
  ]
]
```
## 如何检索

在orm中，可直接用DB门面类来执行MySQL操作，相当于实例化`Illuminate\Database\Query\Builder`类。

那么ES应该也可以这样操作，所以我也加入了一个`ElasticBuilder`类

当然`Model`模型依赖可以按原来的方式操作，最终都是`ElasticBuilder`来构造查询参数
```php
(new ElasticBuilder())
    ->select(['id'])
    ->from('index')
    ->where(function (ElasticBuilder $query) {
        $query->where('name', '张三');
    })->should(function (ElasticBuilder $query) {
        $query->where(
            'id', 1
        )->where(
            'id', 2
        );
    })->whereNotIn(
        'name', ['李四']
    )->dd();

User::elastic()
    ->where(function (Builder $query) {
        $query->where('name', '张三');
    })->should(function (Builder $query) {
        $query->where(
            'id', 1
        )->where(
            'id', 2
        );
    })->whereNotIn(
        'name', ['李四']
    )->dd();
```
最终打印的结果是（这里我仅贴了一个，唯一 的区别就是'index'不同）
```bash
array:3 [
  "index" => "index"
  "_source" => array:1 [
    0 => "id"
  ]
  "body" => array:1 [
    "query" => array:1 [
      "bool" => array:3 [
        "must" => array:1 [
          0 => array:1 [
            "bool" => array:1 [
              "must" => array:1 [
                0 => array:1 [
                  "term" => array:1 [
                    "name" => "张三"
                  ]
                ]
              ]
            ]
          ]
        ]
        "should" => array:1 [
          0 => array:1 [
            "bool" => array:1 [
              "must" => array:2 [
                0 => array:1 [
                  "term" => array:1 [
                    "id" => "1"
                  ]
                ]
                1 => array:1 [
                  "term" => array:1 [
                    "id" => "2"
                  ]
                ]
              ]
            ]
          ]
        ]
        "must_not" => array:1 [
          0 => array:1 [
            "terms" => array:1 [
              "name" => array:1 [
                0 => "李四"
              ]
            ]
          ]
        ]
      ]
    ]
  ]
]
```

直接通过ES检索数据

```
User::elastic()->getRaw()
```

通过ES检索数据后，执行MySQL查询
```
User::elastic()->get();
```
该操作支持`with`,`withCount`


直接通过ES检索数据后分页
```
User::elastic()->paginateRaw();
```

通过ES检索后，执行MySQL查询并分页

```
User::elastic()->paginate();
```

