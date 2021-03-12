# 使用说明

## 生成 Mapping

```php
<?php
namespace App\Entities;


use Golly\Elastic\Entity;
use Golly\Elastic\Annotations\Mapping;
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
     * @return array
     * @throws ElasticException
     */
    public static function mapping()
    {
        $mapping = parent::mapping();
        $mapping['address'] = [
            'properties' => UserAddressEntity::mapping()
        ];

        return $mapping;
    }

    /**
     * @param array $data
     * @return UserEntity
     * @throws ElasticException
     */
    public function toObject(array $data)
    {
        $entity = parent::toObject($data);
        if ($entity->address) {
            foreach ($entity->address as &$address) {
                $address = UserAddressEntity::instance($address);
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


### 如何查询

在实例中引用

```php
<?php
namespace App\Models;

use Golly\Elastic\Eloquent\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class User extends Model{
    use Searchable;
    
    
    /**
     * @param Builder $query
     * @return void
     */
    protected function beforeAllSearchable(Builder $query)
    {
        $query->with(['tokens']);
    }
}
```

```php
<?php
use App\Models\User;
use Golly\Elastic\DSL\Queries\BoolQuery;

$users = User::newElastic()
    ->where('is_active', true)
    ->where(function (BoolQuery $query) {
        $query->orWhereLike('title', 'PHP')
            ->orWhereLike('title', 'JAVA');
    })
    ->whereHas('roles', function (BoolQuery $query) {
        $query->where('name', 'admin');
    })
    ->whereLike('name', '茅台小王子')
    ->orderBy('id')
    ->sum('id')
    ->raw();

?>
```
