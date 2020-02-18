<?php


namespace App\Utils;


use App\Twig\AppExtension;
use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeFrontPage extends CategoryTreeAbstract
{
    /**
     * @var AppExtension
     */
    private $slugger;
    public $mainParentName;
    public $mainParentId;
    public $currentCategoryName;

    public function getCategoryListAndParent(int $id):string
    {
        $this->slugger = new AppExtension();
        $parentData = $this->getMainParent($id);
        $this->mainParentName = $parentData['name'];
        $this->mainParentId = $parentData['id'];

        $key = array_search($id, array_column($this->categoriesArrayFromDb,'id'));
        $this->currentCategoryName = $this->categoriesArrayFromDb[$key]['name'];
        $categories_array = $this->buildTree($parentData['id']);

        return $this->getCategoryList($categories_array);
    }

    public function getCategoryList(array $categories)
    {
        $this->categorylist .= '<ul>';
        foreach ($categories as $value) {
            $catName = $this->slugger->slugify($value['name']);
            $url = $this->urlGenerator->generate('video_list',['categoryName'=> $catName, 'id' => $value['id']]);
            $this->categorylist .= '<li><a href="' . $url . '">' . $catName . '</a>';
            if (!empty($value['children'])) {
                $this->getCategoryList($value['children']);
            }
            $this->categorylist .= '</li>';
        }
        $this->categorylist .= '</ul>';

        return $this->categorylist;
    }

    public function getMainParent(int $id):array
    {
        foreach ($this->categoriesArrayFromDb as $key => $item) {
            if ($item['id'] == $id) {
                return is_null($item['parent']) ? $item : $this->getMainParent($item['parent']);
            }
        }
        throw new \RuntimeException();
    }
}
