<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Post;
use AppBundle\Form\PostType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        // 'default/index.html.twigを表示。変数base_dirをわたす。
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }

    /**
     * @Route("/testblog", name="testblog")
     */
    public function testblogAction(Request $request){
        $name = "takigawa";
        $names = ['taro', 'maro', 'taki', 'yone', 'kodai'];
        // 'default/testblog.html.twigを表示。変数name,namesをわたす。
        return $this->render('default/testblog.html.twig', ["name" => $name, "names" => $names]);
    }
    // 記事一覧、記事の詳細、記事の作成、記事の編集
    // 記事の作成、へんしゅう、削除
    //DB table news

    /**
     * @Route("/list", name="list")
     * @Method("GET")
     */
    public function listAction(){
        // entity managerを定義。
        $em = $this->getDoctrine()->getManager();
        // $postRepositoryを定義。 entity managerのメソッドを使って"AppBundle:Post"を引き数に渡して
        // DBの[Post]テーブルを把握させるため
        $postRepository = $em->getRepository("AppBundle:Post");
        // Postテーブルから全て取り出す。
        $allPosts = $postRepository->findAll(); 
        // post/list.html.twigを表示。変数allをわたす
        $post = new Post();
        $form = $this->cForm($post);
        return $this->render('post/list.html.twig', ['all' => $allPosts, 'form' => $form->createView()]);
        
    }

    /**
     * @Route("/detail", name="detail")
     * @Method("GET")
     */
    public function detailAction(Request $request){

    }

    /**
     * @Route("/new", name="new")
     * @Method("GET")
     */
    public function newAction(Request $request){
        // $hoge = $request->request->postDAta();

        //Postのインスタンスを定義
        $post = new Post();
        // cForm関数に$postをわたして、formに代入
        // form関数で自動的にformを作ることが目的
        // cForm関数は下でprivateで定義
        $form = $this->cForm($post);
        // 'post/new.html.twig'を表示。変数formをわたす
        // form->createView()はHTMLのformを作成
        return $this->render('post/new.html.twig', ['form' => $form->createView()]);
        // $post->setTitle($hoge['title']);
    }

    private function cForm($entity) {
        // formタグをマークアップするときのoption?を定義している。
        $form = $this->createForm(new PostType(), $entity, [
            // actionを定義
            'action' => $this->generateUrl('create'),
            // methodを定義
            'method' => 'POST'
        ]);

        // formタグの自動生成した時にsubmitButtonを作るようにする
        $form->add('submit', 'submit');
        return $form;
    }

    /**
     * @Route("/create", name="create")
     * @Method("POST")
     */
    public function createAction(Request $request){
        // postに空のentityを定義
        $post = new Post();
        // formを作成
        $form = $this->cForm($post);
        // $requestで送られてきた値をformオブジェクトに取り込む
        $form->handleRequest($request);
        // entity manager を定義
        $em = $this->getDoctrine()->getManager();
        // もし$formが変数を持っていたら、
        if($form->isValid()) {
            // $postは$this->cForm($post);によって書き換えられてい(参照渡し)
            // DBへの通信を減らすための何か
            $em->persist($post);
            // DBを更新
            $em->flush();
        }
        return $this->redirect($this->generateUrl('list'));
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @Method("GET")
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $postRepository = $em->getRepository("AppBundle:Post");
        $post = $postRepository->find($id);
        $form = $this->createUpdatePostForm($post);
        return $this->render('post/edit.html.twig', ['post' => $post, 'form' => $form->createView()]);
    }

    /**
     * @Route("/edit/{id}", name="update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $postRepository = $em->getRepository("AppBundle:Post");
        $post = $postRepository->find($id);
        $form = $this->createUpdatePostForm($post);
        $form->handleRequest($request);
        if($form->isValid()) {
            // $postは$this->cForm($post);によって書き換えられてい(参照渡し)
            // DBへの通信を減らすための何か
            $em->persist($post);
            // DBを更新
            $em->flush();
        }else{
            dump($form->getErrors(true, false));
        }
        exit;  
        return $this->redirect($this->generateUrl('list'));
    }

    private function createUpdatePostForm($entity) {
        // formタグをマークアップするときのoption?を定義している。
        $form = $this->createForm(new PostType(), $entity, [
            // actionを定義
            'action' => $this->generateUrl('update', ["id"=> $entity->getId() ]),
            // methodを定義
            'method' => 'PUT'
        ]);

        // formタグの自動生成した時にsubmitButtonを作るようにする
        $form->add('submit', 'submit');
        return $form;
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $postRepository = $em->getRepository("AppBundle:Post");
        $post = $postRepository->find($id);   //$post->idが飛んでくると想定
        $em->remove($post);
        $em->flush();
        return $this->redirect($this->generateUrl('list'));
    }
}
