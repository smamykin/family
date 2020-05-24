<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Product;


class UploadFileController extends AbstractController
{
    private $validator;
    /**
     * @var string
     */
    private $fileSaveDb;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function __invoke(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $fileName = '';

        $productId = $request->request->get('product_id');
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($productId);
        if ($product && $product->getImage() == null) {
            $fileName = $this->_upload($request);
            $product->setImage($this->fileSaveDb);
            $em->persist($product);
            $em->flush();
        }

        return new Response($fileName);

    }

    private function _upload($request)
    {

        $emailConstraint = new Assert\File(['maxSize' => '1000k', 'mimeTypes' => ["image/jpeg", "image/png"]]);

        $errors = $this->validator->validate(
            $request->files->get('imageFile'),
            $emailConstraint
        );

        if (0 === count($errors)) {
// ... this IS a valid email address, do something
        } else {
// this is *not* a valid email address
            $errorMessage = $errors[0]->getMessage();

            return new Response($errorMessage);
        }

        $uploadedFile = $request->files->get('imageFile');


        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is requidred');
        }

        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename
        );
        $fileName = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        try {
            $uploadedFile->move('uploads/images/products',
                $this->fileSaveDb = $originalFilename . '.' . $uploadedFile->guessExtension());
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            return;
        }

        return $fileName;
    }
}


