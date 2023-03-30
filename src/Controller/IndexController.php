<?php 
namespace App\Controller; 
use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\Routing\Annotation\Route; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Category; 
use App\Form\CategoryType; 
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Entity\CategorySearch; 
use App\Form\CategorySearchType;
use App\Entity\PriceSearch;
use App\Form\PriceSearchType;



class IndexController extends AbstractController 
{ 
    /** 
      *@Route("/article/save") 
    */ 

    public function save(EntityManagerInterface$entityManager):Response { 
       
      $article = new Article(); 
      $article->setNom('Article3 '); 
      $article->setPrix(3000); 
      $entityManager->persist($article);
       $entityManager->flush();
        return new Response('Article enregisté avec id '.$article->getId()); 
      }

   /** 
      *@Route("/",name="article_list") 
   */ 
   public function home(EntityManagerInterface$entityManager ,Request $request) 
   { $propertySearch = new PropertySearch(); 
    $form = $this->createForm(PropertySearchType::class,$propertySearch); 
    $form->handleRequest($request);  
    $articles= []; 
    if($form->isSubmitted() && $form->isValid()) 
    { $nom = $propertySearch->getNom(); 
      if ($nom!="")  
      $articles= $entityManager->getRepository(Article::class)->findBy(['nom' => $nom] ); 
      else  
      $articles= $entityManager->getRepository(Article::class)->findAll(); 
    } 
      return $this->render('articles/index.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]); 
    }


    /** 
      * * @Route("/article/new", name="new_article") 
      * * Method({"GET", "POST"})
    */ 
    public function new(EntityManagerInterface$entityManager ,Request $request) { 
      $article = new Article(); 
      $form = $this->createForm(ArticleType::class,$article); 
      $form->handleRequest($request); 
      if($form->isSubmitted() && $form->isValid()) 
      { 
        $article = $form->getData();  
        $entityManager->persist($article); $entityManager->flush(); 
        return $this->redirectToRoute('article_list'); 
      } 
      return $this->render('articles/new.html.twig',['form' => $form->createView()]);

    }

    /** 
      * @Route("/article/{id}", name="article_show") 
    */ 
    public function show(EntityManagerInterface$entityManager,$id) { 
      $article = $entityManager->getRepository(Article::class)
        ->find($id);
      return $this->render('articles/show.html.twig', array('article' => $article)); 
    }

    /** 
      * @Route("/article/edit/{id}", name="edit_article") * Method({"GET", "POST"}) 
    */
    public function edit(EntityManagerInterface$entityManager,Request $request, $id) { 
      $article = new Article(); 
      $article = $entityManager->getRepository(Article::class)->find($id); 
      $form = $this->createForm(ArticleType::class,$article); 
      $form->handleRequest($request); 
      if($form->isSubmitted() && $form->isValid()) 
      {  
        $entityManager->flush(); return $this->redirectToRoute('article_list'); 
      } 
      return $this->render('articles/edit.html.twig', ['form' =>$form->createView()]);
    }

    /** 
      * @Route("/article/delete/{id}",name="delete_article") * @Method({"DELETE"})
    */
    public function delete(EntityManagerInterface$entityManager ,Request $request, $id) { 
      $article = $entityManager->getRepository(Article::class)->find($id); 
      $entityManager->remove($article); 
      $entityManager->flush(); 
      $response = new Response(); 
      $response->send(); 
      return $this->redirectToRoute('article_list'); 
    }

    /** 
      * @Route("/category/newCat", name="new_category") 
      * Method({"GET", "POST"}) 
    */ 
    public function newCategory(EntityManagerInterface$entityManager,Request $request) { 
      $category = new Category(); 
      $form = $this->createForm(CategoryType::class,$category); 
      $form->handleRequest($request); 
      if($form->isSubmitted() && $form->isValid()) 
      { 
        $article = $form->getData();  
        $entityManager->persist($category); 
        $entityManager->flush(); 
      } 
      return $this->render('articles/newCategory.html.twig',['form'=> $form->createView()]); 
    }


    /** 
      * @Route("/art_cat/", name="article_par_cat") 
      * Method({"GET", "POST"}) 
    */ 
    public function articlesParCategorie(EntityManagerInterface$entityManager,Request $request) 
    { $categorySearch = new CategorySearch(); 
      $form = $this->createForm(CategorySearchType::class,$categorySearch); 
      $form->handleRequest($request); $articles= [];
      if($form->isSubmitted() && $form->isValid()) 
      { 
        $category = $categorySearch->getCategory(); 
        if ($category!="") 
        $articles= $category->getArticles(); 
        else 
        $articles= $entityManagergetRepository(Article::class)->findAll(); 
      } 
      return $this->render('articles/articlesParCategorie.html.twig',['form' => $form->createView(),'articles' => $articles]); 
    }

    /** 
      * @Route("/art_prix/", name="article_par_prix") 
      * Method({"GET"}) 
    */ 
    public function articlesParPrix(EntityManagerInterface$entityManager,Request $request) 
    { $priceSearch = new PriceSearch(); 
      $form = $this->createForm(PriceSearchType::class,$priceSearch); 
      $form->handleRequest($request); 
      $articles= []; 
      if($form->isSubmitted() && $form->isValid()) 
      { $minPrice = $priceSearch->getMinPrice();
         $maxPrice = $priceSearch->getMaxPrice(); 
         $articles= $entityManager-> getRepository(Article::class)->findByPriceRange($minPrice,$maxPrice); 
      } 
      return $this->render('articles/articlesParPrix.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);
   }




}