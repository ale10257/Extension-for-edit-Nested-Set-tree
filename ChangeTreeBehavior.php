<?php
namespace ale10257\ext;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class ChangeTreeBehavior extends Behavior
{
    public $rootSite;

    /**
     * @param array $post
     * @return mixed
     */
    public function updateTree($post)
    {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        $one = $owner->find()->where(['id' => $post['first']])->one();
        $two = $owner->find()->where(['id' => $post['two']])->one();
        if ($one && $two) {
            $parent_one = $one->parents(1)->one();
            $parent_two = $two->parents(1)->one();
            if ($parent_one->id == $parent_two->id) {
                if ($post['action'] == 'before') $one->insertBefore($two);
                if ($post['action'] == 'after') $one->insertAfter($two);
            }
        }
        return $this->getTree();
    }

    public function getTree()
    {
        $root = $this->getRoot();
        return $root->children()->all();
    }

    public function createItem($parent)
    {
        /* @var $owner BaseActiveRecord */
        if ($parent) {
            $result['parents'] = $parent;
            $result['parent_id'] = $parent->id;
        } else {
            $root = $this->getRoot();
            $result['parents'] = $root;
            $result['parent_id'] = $root->id;
        }
        $result['parents'] = $this->setRootName($result['parents']);
        return $result;
    }

    public function updateItem($node, $nameFieldForType)
    {
        /* @var $owner BaseActiveRecord */
        /* @var $node BaseActiveRecord */
        /* @var $item BaseActiveRecord */
        /* @var $parent BaseActiveRecord */

        $root = $this->getRoot();

        $parents = $root->children()->andWhere([$nameFieldForType => $node->$nameFieldForType])->all();
        array_unshift($parents, $root);

        $parent = $node->parents(1)->one();

        $childs = [];

        foreach ($node->children()->all() as $item) {
            $childs[$item->primaryKey] = $item;
        }

        $result['parent_id'] = $parent->primaryKey;

        foreach ($parents as $key => $item) {
            if (array_key_exists($item->primaryKey, $childs)) {
                unset($parents[$key]);
            }
            if ($item->primaryKey == $node->primaryKey) {
                unset($parents[$key]);
            }
        }

        $parents[0] = $this->setRootName($parents[0]);
        $result['parents'] = $parents;

        return $result;
    }

    private function setRootName($parent)
    {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if (!empty($owner->root_name)) {
            if ($parent->name == $this->rootSite) {
                $parent->name = $owner->root_name;
            }
        }

        return $parent;
    }

    private function getRoot (){
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        return $owner->findOne(['name' => $this->rootSite]);
    }
}
