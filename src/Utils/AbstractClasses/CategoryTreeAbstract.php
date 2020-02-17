<?php

namespace App\Utils\AbstractClasses;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CategoryTreeAbstract
{
    protected static $dbConnection;
    /**
     * @var array
     */
    protected $categoriesArrayFromDb;
    /**
     * @var string
     */
    protected $categoryList;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * CategoryTreeAbstract constructor.
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->categoriesArrayFromDb = $this->getCategories();
    }

    abstract public function getCategoryList(array $categories);

    public function buildTree(int $parentId = null): array
    {
        $subcategory = [];
        foreach ($this->categoriesArrayFromDb as $category) {
            if ($category['parent'] == $parentId) {
                $children = $this->buildTree($category['id']);
                if ($children) {
                    $category['children'] = $children;
                }

                $subcategory[] =  $category;
            }
        }
        return $subcategory;
    }

    private function getCategories(): array
    {
        if (self::$dbConnection) {
            return self::$dbConnection;
        }

        $conn = $this->entityManager->getConnection();
        $sql = "SELECT * FROM categories";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        self::$dbConnection = $stmt->fetchAll();
        return self::$dbConnection;
    }
}
