# 使用说明

## 生成 Mapping

```php
<?php

namespace App\Entities;


use Golly\Elastic\Entity;
use Golly\Elastic\Annotations\Mapping;
use Golly\Elastic\Annotations\Source;

/**
 * Class UserEntity
 * @package App\Entities
 */
class UserEntity extends Entity
{

    /**
     * @Mapping(type="long")
     * @var int
     */
    public $id;
    
    /**
     * @Mapping(type="text", analyzer="ik_small")
     * @var 
     */
    public $name;

    /**
     * @Mapping(type="date", format="yyyy-MM-dd")
     * @var string
     */
    public $date;
}
?>
```

```php
<?php
use App\Entities\UserEntity;

$entity = new UserEntity();

$entity->mapping();
```

结果如下
```shell
array:3 [
  "id" => array:1 [
    "type" => "long"
  ]
  "name" => array:2 [
    "type" => "text"
    "analyzer" => "ik_small"
  ]
  "date" => array:2 [
    "type" => "date"
    "format" => "yyyy-MM-dd"
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

class User extends Model{
    use Searchable;
    
}
```

```php
<?php
use App\Models\User;

$users = User::newElastic()
    ->where('is_active', true)
    ->where(function (BoolQuery $query) {
        $query->orWhereLike('title', 'PHP')
            ->orWhereLike('title', 'JAVA');
    })
    ->whereLike('name', '茅台小王子')
    ->orderBy('id')
    ->sum('id')
    ->raw();

?>
```
