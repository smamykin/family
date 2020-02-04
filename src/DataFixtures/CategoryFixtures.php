<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use InvalidArgumentException;

class CategoryFixtures extends Fixture
{
    private const ELECTRONICS = 'Electronics';
    private const COMPUTERS = 'Computers';
    private const LAPTOPS = 'Laptops';
    private const BOOKS = 'Books';
    private const MOVIES = 'Movies';
    private const ROMANCE = 'Romance';

    /**
     * @var array
     */
    private $data;

    public function __construct()
    {
        $this->data = [
            self::ELECTRONICS => $this->getElectronicsData(),
            self::COMPUTERS => $this->getComputersData(),
            self::LAPTOPS => $this->getLaptopsData(),
            self::BOOKS => $this->getBooksData(),
            self::MOVIES => $this->getMoviesData(),
            self::ROMANCE => $this->getRomanceData(),
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->loadMainCategories($manager);
        foreach ($this->data as $key => $v) {
            $this->loadSubcategoriesData($manager, null, $key);
        }
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
        /** @var Category $parent */
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
            [self::BOOKS, 3],
            [self::MOVIES, 4],
        ];
    }

    private function getElectronicsData()
    {
        return [
            ['Cameras',5],
            [self::COMPUTERS,6],
            ['Cell Phones',7],
        ];
    }

    private function getComputersData()
    {
        return [
            [self::LAPTOPS, 8],
            ['Desktops', 9],
        ];
    }

    private function getLaptopsData()
    {
        return [
            ['Apple', 10],
            ['Asus', 11],
            ['Dell', 12],
            ['Lenovo', 13],
            ['Hp', 14],
        ];
    }

    private function getBooksData()
    {
        return [
            ['Children\'s books', 15],
            ['Kindle eBooks', 16],
        ];
    }

    private function getMoviesData()
    {
        return [
            ['Family', 17],
            [self::ROMANCE, 18],
        ];
    }

    private function getRomanceData()
    {
        return [
            ['Romantic Comedy', 19],
            ['Romantic Drama', 20],
        ];
    }

    private function getData($parentName)
    {
        return $this->data[$parentName];
    }
}
