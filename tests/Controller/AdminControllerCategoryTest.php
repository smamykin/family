<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Tests\RollAdmin;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerCategoryTest extends WebTestCase
{
    use RollAdmin;
    /**
     * @var KernelBrowser
     */
    private $client;
    /**
     * @var EntityManagerInterface|null
     */
    private $em;
    private $categoryUrl;
    private $categoryEditUrl;
    private $deleteCategoryUrl;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->deleteCategoryUrl = '/admin/su/delete_category/';
        $this->categoryEditUrl = '/admin/su/edit_category/';
        $this->categoryUrl = '/admin/su/categories';
    }

    public function testTextOnPage()
    {
        $crawler = $this->client->request('GET', $this->categoryUrl);
        $this->assertSame('Categories list', $crawler->filter('h2')->text());
        $this->assertStringContainsString('Electronics', $this->client->getResponse()->getContent());
    }

    public function testNumberOfItems()
    {
        $crawler = $this->client->request('GET', $this->categoryUrl);
        $this->assertCount(21, $crawler->filter('option'));
    }

    public function testNewCategory()
    {
        $crawler = $this->client->request('GET', $this->categoryUrl);
        $rep = $this->em->getRepository(Category::class);
        /** @var Category $parent */
        $parent = $rep->findOneBy([]);
        $form = $crawler->selectButton('Add')->form([
            'category[parent]' => $parent->getId(),
            'category[name]' => 'Other electronics',
        ]);
        $this->client->submit($form);

        $category = $rep->findOneBy(
            ['name'=>'Other electronics']
        );

        $this->assertNotNull($category);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function testEditCategory()
    {
        /** @var CategoryRepository $rep */
        $rep = $this->em->getRepository(Category::class);
        $parent = $rep->findOneBy([]);
        $cat = $rep->findOneByIdNotEqual($parent->getId());
        $crawler = $this->client->request('GET', $this->categoryEditUrl . $cat->getId());
        /** @var Category $parent */
        $form = $crawler->selectButton('Save')->form(
            [
                'category[parent]' => $parent->getId(),
                'category[name]' => 'Electronics 2',
            ]
        );
        $this->client->submit($form);

        $category = $rep->find($cat->getId());
        $this->assertSame('Electronics 2', $category->getName());
    }

    public function testDeleteCategory()
    {
        /** @var CategoryRepository $rep */
        $rep = $this->em->getRepository(Category::class);
        $cat = $rep->findOneBy([]);
        $id = $cat->getId();
        $crawler = $this->client->request('GET', $this->deleteCategoryUrl . $id);

        $category = $rep->find($id);
        $this->assertNull($category);
    }
}