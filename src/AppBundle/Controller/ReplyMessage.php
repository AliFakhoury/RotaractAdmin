<?php

namespace AppBundle\Controller;
use Swift_Mailer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReplyMessage extends Controller{
    /**
     * @Route("/Admin/ViewMessages/Reply/{id}",name="ReplyMessage")
     */
    public function ViewMessagesAction(Request $request, $id){
        $session = new Session();
        $user = $this->getUser();
        $PAGE_ID = 13;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());

        $form=$this->formCreate();
        $form->handleRequest($request);

        $messageRepo = $this->getDoctrine()->getRepository('AppBundle:Message');
        $message = $messageRepo->getMessageById($id);
        dump($message);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $this->sendEmail($data["reply"]);
        }

        return $this->render('Admin/ViewMessages/ReplyMessage.html.twig',[
            'userPages' => $systemPages,
            'form' => $form->createView(),
            'message' => $message,

        ]);
    }

    public function sendEmail($reply){
        $message = \Swift_Message::newInstance()
            ->setSubject('Support Form Submission')
            ->setFrom("ali@ali.com")
            ->setTo('fakhouryali@yahoo.com')
            ->setBody(
                $reply,
                'text/plain'
            )
        ;

        $this->get('mailer')->send($message);
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