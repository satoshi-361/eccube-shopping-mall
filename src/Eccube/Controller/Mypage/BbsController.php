<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Mypage;

use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\EntryType;
use Eccube\Repository\CustomerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Eccube\Entity\Customer;
use Eccube\Entity\Member;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\BbsRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Eccube\Form\Type\Front\BbsType;
use Eccube\Util\StringUtil;
use Eccube\Repository\MemberRepository;

class BbsController extends AbstractController
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;
    
    /**
     * @var OrderRepository
     */
    protected $orderRepository;
    
    /**
     * @var BbsRepository
     */
    protected $bbsRepository;
    
    /**
     * @var MemberRepository
     */
    protected $memberRepository;
    

    public function __construct(
        CustomerRepository $customerRepository,
        EncoderFactoryInterface $encoderFactory,
        TokenStorageInterface $tokenStorage,
        OrderRepository $orderRepository,
        BbsRepository $bbsRepository,
        MemberRepository $memberRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->encoderFactory = $encoderFactory;
        $this->tokenStorage = $tokenStorage;
        $this->orderRepository = $orderRepository;
        $this->bbsRepository = $bbsRepository;
        $this->memberRepository = $memberRepository;
    }

    /**
     * BBS
     *
     * @Route("/bbs/{id}", name="bbs", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     * @Template("Bbs/index.twig")
     */
    
    public function index(Request $request, Order $Order)
    {
        $Customer = $this->getUser();
        $Member = null;
        
        $is_admin = $this->session->has('_security_admin');
        
        if($is_admin){
            $Member = $this->memberRepository->find($this->session->get('eccube.bbs'));
        }
        
        if (!$Order || $Order->getOrderStatus()->getId() != OrderStatus::PAID) {
            throw new NotFoundHttpException();
        }
        
        $OrderItems = $Order->getOrderItems();
        
        $Product = null;
        
        foreach ($OrderItems as $OrderItem) {
            if ($OrderItem->isProduct()) {
                $Product = $OrderItem->getProduct();
                break;
            }
        }
        
        if(is_null($Product)){
            throw new NotFoundHttpException();
        }
        
        if(is_null($Member)){
            if(is_null($Customer) || $Order->getCustomer()->getId() != $Customer->getId()){
                throw new NotFoundHttpException();
            }
        }
        else{
            
            $Creator = $Product->getCreator();
            
            if(is_null($Creator) ||  $Creator->getId() != $Member->getId()){
                throw new NotFoundHttpException();
            }
        }
        
        $BbsList = $this->bbsRepository->findBy(
            [
                'Order' => $Order,
            ]
        );
        
        $BbsImgList = $this->bbsRepository->findBy(
            [
                'Order' => $Order,
            ],
            [
                'id' => 'DESC',
            ]
        );
        
        $imgCount = 0;
          
        foreach ($BbsImgList as $Bbs) {
            if(!is_null($Bbs->getImage())){
                $imgCount++;
            }
        }
        
        $count = $this->bbsRepository->getMaxCount($Order);
             
        $builder = $this->formFactory
        ->createNamedBuilder('', BbsType::class);
        
        $form = $builder->getForm();
        
        $form->handleRequest($request);
           
        if ($form->isSubmitted() && $form->isValid()) {
            
            $newFilename = null;
            
            $imageFile = $form->get('filename_file')->getData();
            
            if($imageFile != ""){
                $uniqueName = StringUtil::random(128);
                $ext = $imageFile->guessExtension();
                
                $newFilename = $uniqueName.'.'.$ext;
                
                $imageFile->move(
                    $this->getParameter('eccube_save_file_dir'),$newFilename
                    );
            }
            
            $Bbs = new \Eccube\Entity\Bbs();
            
            $Bbs
            ->setMessage($form->get('message')->getData())
            ->setImage($newFilename)
            ->setOrder($Order)
            ->setCustomer($Customer)
            ->setCreator($Member);
            
            $this->entityManager->persist($Bbs);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('bbs', ['id' => $Order->getId()]);
            
        }
        
        if ($request->isXmlHttpRequest()) {
            
            
            $JsBbsList = $this->bbsRepository->getNewBbs($Order,intval($request->get('count')));
            
            $message = array();
            $image = array();
            
            foreach ($JsBbsList as $Bbs) {
                
                $customerFlg = 0;
                $imageFlg = 1;
                $name = "";
                
                if(is_null($Bbs->getCreator())){
                    $customerFlg = 1;
                    $name = $Bbs->getCustomer()->getName01().$Bbs->getCustomer()->getName02();
                }
                else{
                    $name = $Bbs->getCreator()->getName();
                }
                
                if(is_null($Bbs->getImage())){
                    $imageFlg = 0;
                }
                else{
                    $image[] = ['img' => $Bbs->getImage()];
                }
                
                $message[] = ['msg' => $Bbs->getMessage(),'name' => $name ,'date' => $Bbs->getCreateDate()->format('Y年m月d日 H時i分'),'customer_flg' => $customerFlg,'image_flg' => $imageFlg];
            }
            
            return $this->json(['message' => $message, 'image' => $image, 'count' => $count]);
        }
           
        return [
            'BbsList' => $BbsList,
            'BbsImgList' => $BbsImgList,
            'Order' => $Order,
            'Product' => $Product,
            'form' => $form->createView(),
            'count' => $count,
            'imgCount' => $imgCount,
        ];
        
    }
    
    /**
     * 
     *
     * @Route("/bbs/{id}/post", name="bbs_post", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     */
    public function bbsPost(Request $request, Order $Order)
    {
        
        /*if (!$Order || $Order->getOrderStatus()->getId() != OrderStatus::PAID) {
            throw new NotFoundHttpException();
        }
        
        $builder = $this->formFactory
        ->createNamedBuilder('', BbsType::class);
        
        $form = $builder->getForm();
        
        
        return $this->redirectToRoute('bbs', ['id' => $Order->getId()]);*/
    }
    
}
