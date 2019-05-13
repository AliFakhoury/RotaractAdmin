<?php

namespace AppBundle\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ViewMessages extends Controller{
    /**
     * @Route("/Admin/ViewMessages/{pageNumber}",name="ViewMessages")
     */
    public function ViewMessagesAction(Request $request, $pageNumber){
        $session = new Session();
        $user = $this->getUser();

        $PAGE_ID = 13;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }
        
        $language = $session->get('locale');
        
        $repo = $this->getDoctrine()->getRepository('AppBundle:Message');
        $request->setLocale($language);
        
        $userRole = $session->get('userRole');
        
        $countPages = $repo->countPages(10);
        
        $messages = $repo->findAllByPage(10, $pageNumber);
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());

        $form=$this->formCreate();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();


        }


        return $this->render('Admin/ViewMessages/ViewMessages.html.twig',[
            'messages'=>  $messages,
            'language' => $language,
            'userRole' => $userRole,
            'pageNumber' => $pageNumber,
            'numPages' => $countPages,
            'userPages' => $systemPages,
        ]);
    }

    public function checkRoles($pageId){
        $repository = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $repositoryRoles = $this->getDoctrine()->getRepository('AppBundle:UsersRoles');
        $repositoryPermissions = $this->getDoctrine()->getRepository('AppBundle:Permission');

        $userId = $this->getUser()->getAdminId();

        $roleOfPage = $repository->findRolesByPageId($pageId);
        $rolesOfUser = $repositoryRoles->getRolesById($userId);

        return in_array($roleOfPage['role_id'], $rolesOfUser, false) && $repositoryPermissions->findByUserRoleId($userId, $roleOfPage);
    }

    public function formCreate(){
        $form = $this->createFormBuilder()
            ->add('reply', TextType::class)
            ->add('send', SubmitType::class)
            ->getForm();

        return $form;
    }
}