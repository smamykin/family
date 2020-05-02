<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdminControllerCategoryTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    private $client;
    /**
     * @var EntityManagerInterface|null
     */
    private $em;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->rollback();
        $this->em->close();
        $this->em = null;
    }

    public function testTextOnPage()
    {
        $crawler = $this->client->request('GET', '/admin/categories');
        $this->assertSame('Categories list', $crawler->filter('h2')->text());
        $this->assertStringContainsString('Electronics', $this->client->getResponse()->getContent());
    }

    public function testNumberOfItems()
    {
        $crawler = $this->client->request('GET', '/admin/categories');
        $this->assertCount(21, $crawler->filter('option'));
    }

    public function testNewCategory()
    {
        $crawler = $this->client->request('GET', '/admin/categories');
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
        $crawler = $this->client->request('GET', '/admin/edit_category/' . $cat->getId());
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
        $crawler = $this->client->request('GET', '/admin/delete_category/' . $id);

        $category = $rep->find($id);
        $this->assertNull($category);
    }
}
