<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_home")
     */
    public function home(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([],['price'=>'DESC'],5);
        return $this->render('product/home.html.twig', ['products'=>$products

        ]);
    }
    /**
     * @Route("/list", name="product_list")
     */
    public function list(ProductRepository $productRepository): Response
    {
    $products = $productRepository->findAll();

        return $this->render('product/list.html.twig', ['products'=>$products

        ]);
    }
    /**
     * @Route("/add/{id}", name="product_add",defaults={"id"=""})
     */
    public function add(EntityManagerInterface $entityManager,Request $request,ProductRepository $productRepository): Response
    {
         $edit = false;
        if($request->get('id') != null)
        {
            $product = $productRepository->find($request->get('id'));
            $edit = true;
        }
        else{
            $product = new Product();
            $product->setDateAdd(new \DateTime());
        }

    $form = $this->createForm(ProductType::class,$product);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid() && !$edit)
    {
        $product = $form->getData();

        $entityManager->persist($product);
        $entityManager->flush();
        $this->addFlash('success','Your product has successfully been added!');
        return $this->redirectToRoute('product_list');
    }
    elseif ($form->isSubmitted() && $form->isValid())
    {
        $product = $form->getData();
        $entityManager->persist($product);
        $entityManager->flush();
        $this->addFlash('success','Your product has successfully been edit!');
        return $this->redirectToRoute('product_list');
    }
        return $this->renderForm('product/add.html.twig', ['form'=>$form,'edit'=>$edit

        ]);
    }
    /**
     * @Route("/details/{id}", name="product_details")
     */
    public function details(Product $product,ProductRepository $productRepository): Response
    {

        $product = $productRepository->find($product->getId());
        return $this->render('product/details.html.twig', ['product'=>$product

        ]);
    }

}
