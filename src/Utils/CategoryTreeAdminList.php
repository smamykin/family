<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminList extends CategoryTreeAbstract
{

    public function getCategoryList(array $categories)
    {
        $this->categorylist .= '<ul class="fa-ul text-left">';
        foreach ($categories as $value) {
            $catName = $value['name'];
            $urlEdit = $this->urlGenerator->generate('edit_category', ['id' => $value['id']]);
            $urlDelete = $this->urlGenerator->generate('delete_category', ['id'=>$value['id']]);

            $this->categorylist .= <<<HTML
<li>
    <i class="fa-li fa fa-arrow-right"></i>{$catName}
    <a href="{$urlEdit}">edit</a>
    <a onclick="return confirm('Are you sure?');" href="{$urlDelete}">delete</a>
</li>
HTML;
            if (!empty($value['children'])) {
                $this->getCategoryList($value['children']);
            }
            $this->categorylist .= '</li>';
        }
        $this->categorylist .= '</ul>';

        return $this->categorylist;
    }
}
