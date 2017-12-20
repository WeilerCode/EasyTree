<?php

namespace Weiler\EasyTree;

class EasyTree
{
    //树原型
    private $menu;
    //html模板
    private $template = '';
    //组合后的html模型
    private $tree = '';
    //父节点的对象名
    private $parentStr;
    //最高父节点ID
    private $parentValue;
    //分隔符
    private $delimiter = [
        '&nbsp;&nbsp;&nbsp;&nbsp;',
        '├',
        '└'
    ];

    private $continue = [];

    /**
     * EasyTree constructor.
     * @param array $menu 树原型数组对象
     * @param string $parentStr 父节点对象名
     */
    public function __construct($menu, $parentStr = 'parentID', $parentValue = 0)
    {
        $this->menu = $menu;
        $this->parentStr = $parentStr;
        $this->parentValue = $parentValue;
    }

    /**
     * 设置模板
     * @param $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * 设置分隔符
     * @param $delimiter
     * @return $this
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * 获取子目录集合
     * @param $parentValue
     * @return array|bool
     */
    public function getChild($parentValue)
    {
        $child = array_where($this->menu, function($v, $k) use($parentValue)
        {
            return $v[$this->parentStr] == $parentValue;
        });
        return $child ? $child : false;
    }

    /**
     * 创建树目录ID
     * @param $parentValue
     * @return bool
     */
    private function createContinue($parentValue)
    {
        $child = $this->getChild($parentValue);
        if($child)
        {
            $child = array_keys($child);
            $this->continue = array_merge($this->continue, $child);
            foreach ($child as $v)
            {
                $this->getContinue($v);
            }
        }
        return true;
    }

    /**
     * 获取对应ID下的所有目录ID
     * @param $parentValue
     * @return array|bool
     */
    public function getContinue($parentValue)
    {
        if ($this->createContinue($parentValue))
            return $this->continue;
        return false;
    }

    /**
     * 创建目录树
     * @param $parentValue 父ID
     * @param string $delimiterLeft 左侧递归分隔符
     * @return bool
     */
    private function createTree($parentValue, $delimiterLeft = '')
    {
        $n = 1;
        $child = $this->getChild($parentValue);
        if ($child)
        {
            $total = count($child);
            foreach ($child as $key=>$v)
            {
                $j=$k='';
                if ($total == $n)
                {
                    $j .= $this->delimiter[2];
                    $k = $this->delimiter[0];
                }else{
                    $j .= $this->delimiter[1];
                    $k  = $delimiterLeft ? $this->delimiter[0] : '';
                }
                $spacer = $delimiterLeft ? $delimiterLeft.$j : '';

                eval("\$this->tree .= \"$this->template\";");
                $this->createTree($v->id, $delimiterLeft.$k.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                $n++;
            }
        }
        return true;
    }

    /**
     * 获取Html目录树
     * @return string
     */
    public function getTree()
    {
        if ($this->createTree($this->parentValue))
            return $this->tree;
        echo '-_-';
    }
}