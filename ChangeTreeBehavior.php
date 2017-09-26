<?php
namespace ale10257\ext;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class ChangeTreeBehavior extends Behavior
{
    public $rootSite;

    public function updateTree($post, $numTree = 1)
    {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        $one = $owner->find()->where(['tree' => $numTree, 'id' => $post['first']])->one();
        $two = $owner->find()->where(['tree' => $numTree, 'id' => $post['two']])->one();

        $parent_one = $one->parents(1)->one();
        $parent_two = $two->parents(1)->one();

        if ($parent_one->id == $parent_two->id) {
            if ($post['action'] == 'before') $one->insertBefore($two);
            if ($post['action'] == 'after') $one->insertAfter($two);
        }

        return $this->getTree();
    }

    public function getTree()
    {
        $root = $this->getRoot();
        return $root->children()->all();
    }

    private function getRoot (){
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        return $owner->findOne(['name' => $this->rootSite]);
    }
}
