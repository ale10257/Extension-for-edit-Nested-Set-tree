<?php
namespace ale10257\ext;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\Widget;

class GetTreeWidget extends Widget
{
    private $faIcons = [
        'update' => 'pencil-square-o',
        'create' => 'plus',
        'delete' => 'trash-o',
        'leaf' => 'file',
        'folder_open' => 'folder-open parent',
        'folder' => 'folder parent',
    ];

    public function init () {
        if (empty($this->options['data'])) {
            throw new \DomainException('Data not found');
        }
        if (empty($this->options['urlChangeTree'])) {
            throw new \DomainException('urlChangeTree not found');
        }
        if (empty($this->options['urlAddItem'])) {
            throw new \DomainException('urlAddItem not found');
        }
        if (empty($this->options['urlUpdateTree'])) {
            throw new \DomainException('urlUpdateTree not found');
        }
        if (empty($this->options['urlDeleteTree'])) {
            throw new \DomainException('urlDeleteTree not found');
        }
        if (empty($this->options['fieldForTitleItem'])) {
            throw new \DomainException('fieldForTitleItem not found');
        }

        if (!empty($this->options['faUpdate'])) {
            $this->faIcons['update'] = $this->options['faUpdate'];
        }

        if (!empty($this->options['faCreate'])) {
            $this->faIcons['create'] = $this->options['faCreate'];
        }
        if (!empty($this->options['faDelete'])) {
            $this->faIcons['delete'] = $this->options['faDelete'];
        }

        foreach ($this->faIcons as $key => $item) {
            $this->faIcons[$key] = Html::tag('i', '', ['class' => 'fa fa-' . $item]);
        }
        parent::init();
    }

    public function run()
    {
        TreeAssets::register(Yii::$app->getView());
        return $this->createTree();
    }

    private function createTree () {

        $data = $this->options['data'];
        $urlAddItem = $this->options['urlAddItem'];
        $urlChangeTree = $this->options['urlChangeTree'];
        $urlUpdateTree = $this->options['urlUpdateTree'];
        $urlDeleteTree = $this->options['urlDeleteTree'];
        $fieldForTitleItem = $this->options['fieldForTitleItem'];

        $str = '<div data-url=" ' . $urlChangeTree . '"  id="tree-table"><table class="sortable"><tbody>';
        $i = 0;

        foreach ($data as $key => $item) {

            $options = ['data-level' => $item->depth, 'data-id' => $item->id];

            $urlUpdate = Html::a($this->faIcons['update'], Url::to([$urlUpdateTree, 'id' => $item->id]));

            $urlDelete = Html::a($this->faIcons['delete'], Url::to([$urlDeleteTree, 'id' => $item->id]), ['class' => 'delete-item-tree']);

            $urlAdd = Html::a($this->faIcons['create'], Url::to([$urlAddItem, 'parent_id' => $item->id]));

            $td2 = Html::tag('td', $urlUpdate . $urlDelete . $urlAdd, ['style' => 'width: 100px;']);

            $span = $this->faIcons['leaf'] . ' ';

            if ((!(empty($data[$key + 1])) && $data[$key + 1]['depth'] == $item->depth && $item->depth == 1) ||( $item->depth == 1 && empty($data[$key + 1])) ) {
                $i = 0;
                $td1 =  Html::tag('td', $span . $item->$fieldForTitleItem);
                $str .= Html::tag('tr', $td1 . $td2, $options);
            }
            if ((!(empty($data[$key + 1])) && $data[$key + 1]['depth'] > $item->depth) || $i > 0) {
                $i++;
                $options_td = [];
                $options['class'] = 'hide-show';
                if ($item->depth > 1) {
                    $options_td = ['style' => 'padding-left: ' . $item->depth * 20 . 'px;'];
                }

                if (!(empty($data[$key + 1])) && $data[$key + 1]['depth'] > $item->depth) {
                    $span = $this->faIcons['folder_open'] . ' ';
                }
                $td1 = Html::tag('td', $span . $item->$fieldForTitleItem, $options_td);
                $str .= Html::tag('tr', $td1 . $td2, $options);
            }
        }
        $str .= '</tbody></table></div>';
        return $str;
    }
}
