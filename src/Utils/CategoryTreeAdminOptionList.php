<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminOptionList extends CategoryTreeAbstract
{

    public function getCategoryList(array $categories, $repeat = 0)
    {
        foreach ($categories as $value) {
            $this->categorylist[] = [
                'name' => str_repeat('-', $repeat) . $value['name'], 'id'   => $value['id'],
            ];

            if (!empty($value['children'])) {
                $this->getCategoryList($value['children'], $repeat + 2);
            }
        }
    }
}
