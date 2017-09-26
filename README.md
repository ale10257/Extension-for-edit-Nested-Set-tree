Extension for edit Nested Set tree
==================================
Include widget and behavior for edit  Nested Set tree

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require ale10257/yii2-ext-for-work-nested-set "dev-master"
```

or add

```
"ale10257/yii2-ext-for-work-nested-set": "dev-master"
```

to the require section of your `composer.json` file.

Capabilities
------------

1. Generating a menu tree as a table (use Nested Set tree)

2. Drag & Drop rows table

3. Save result in DB

**Example table before move**

![before move](http://kulagin-alex.ru/no-delete-images/tree1.png)

**Move**

![move](http://kulagin-alex.ru/no-delete-images/tree2.png)

**After move**

![after move](http://kulagin-alex.ru/no-delete-images/tree3.png)

***Important***
------
**Only siblings elements can be dragged!!!**

Usage
-----

**Model**

Set up the model as here: https://github.com/creocoder/yii2-nested-sets
and determine the constant SITE_ROOT_NAME and behaviors

```php
<?php
use creocoder\nestedsets\NestedSetsBehavior;
use ale10257\ext\ChangeTreeBehavior;

...

const SITE_ROOT_NAME = 'My_SITE_ROOT';

...

    public function behaviors()
    {
        return [
            [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
            ],
            [
                'class' => ChangeTreeBehavior::className(),
                'rootSite' => SITE_ROOT_NAME
            ]
        ];
    }
      
...
?>
```
**View index.php for Category**

```php
...
    <?php if($data) : ?>
        <div class="category-index">
            <h1><?= Html::encode($this->title) ?></h1>
                <?= $this->render('tree', ['data' => $data]) ?>
        </div>
    <?php endif ?>
...
```

**View tree.php**
```php
<?php
use ale10257\ext\GetTreeWidget;
use yii\helpers\Url;
//Number tree in your Nested Set tree
$numTree = 1;

echo GetTreeWidget::widget([
        'options' => [
            'data' => $data,
            // action for ajax request for edit tree
            'urlChangeTree' => Url::to(['/admin/category/update-tree', 'numTree' => 1]), 
            'urlUpdateTree' => Url::to(['/admin/category/update']),
            'urlDeleteTree' => Url::to(['/admin/category/delete']),
            'urlAddItem' => Url::to(['/admin/category/create']),
            // name field in your table for view
            'fieldForTitleItem' => 'name',
        ]
    ]); 
?>
```
**Controller**
```php
<?php
...
    
    public function actionIndex()
    {
        $category = new Category;
        return $this->render('index', [
            'data' => $category->getTree(),
        ]);
    }
...  
    //accepts the ajax request  
    public function actionUpdateTree($numTree = 1)
    {
        if (Yii::$app->request->isAjax) {
            $model = new Category();
            $post = Yii::$app->request->post();
            return $this->renderPartial('tree', ['data' => $model->updateTree($post, $numTree)]);
        }
        return Yii::$app->request->referrer;
    }    
...
?>
```
