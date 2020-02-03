<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use InvalidArgumentException;

class CategoryFixtures extends Fixture
{
    private const ELECTRONICS = 'Electronics';
    /**
     * @var array
     */
    private $data;

    public function __construct()
    {
        $this->data = [
            self::ELECTRONICS => $this->getElectronicsData(),
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->loadMainCategories($manager);
        $this->loadSubcategoriesData($manager, null, self::ELECTRONICS);
    }

    private function loadMainCategories(ObjectManager $manager)
    {
        foreach ($this->getMainCategoriesData() as [$name]) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
        }

        $manager->flush();
    }

    private function loadSubcategoriesData(ObjectManager $manager, $category, $parentName)
    {
        $parent = $manager->getRepository(Category::class)->findOneBy(['name' => $parentName]);

        if (empty($parent)) {
            throw new InvalidArgumentException();
        }

        foreach ($this->getData($parentName) as [$name]) {
            $category = new Category();
            $category->setName($name);
            $category->setParent($parent);
            $manager->persist($category);
        }

        $manager->flush();
    }

    private function getMainCategoriesData()
    {
        return [
            [self::ELECTRONICS,1],
            ['Toys',2],
            ['Books',3],
            ['Movies',4],
        ];
    }

    private function getElectronicsData()
    {
        return [
            ['Cameras',5],
            ['Computers',6],
            ['Cell Phones',7],
        ];
    }

    private function getData($parentName)
    {
        return $this->data[$parentName];
    }
}
