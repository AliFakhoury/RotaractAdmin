<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/26/2018
 * Time: 9:15 AM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Role;
use AppBundle\Entity\UsersRoles;
use AppBundle\Entity\usersRolesRepository;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;

class EditAdminController extends Controller{

    /**
     * @Route("/Admin/ManageAdmins/UpdateAdmin/{id}/{pageNumber}",name="UpdateAdmins")
     */
    public function UpdateAdminAction(Request $request,$id, $pageNumber){
        $session = new Session();

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        $PAGE_ID = 1;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        if($permissions->getCanEdit() == 0){
            die("You cannot enter this page.");
        }
        
        $systemPages = $session->get('systemPages');
        
        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();

        $language = $session->get('locale');
        $request->setLocale($language);

        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormAdmin($id);
        $form->handleRequest($request);

        $test = $em->getRepository('AppBundle:admin')->find($id);

        $userEmail = $form->getData()['Email'];
        $message = "Updated Successfully";

        if($form->isSubmitted() && $form->isValid()){
            $userRolesRepository = $this->getDoctrine()->getRepository('AppBundle:UsersRoles');
            $roles = $userRolesRepository->findBy([
                'user_id' => $id,
            ]) ;

            if (isset($roles)) {
                foreach($roles as $role){
                    $em->remove($role);
                }
            }
            
            try{
                $em->flush();
            }catch ( DBALException  $e ){
                $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'multiple', 'users_roles', $e->getMessage(), $this->getIpAddress());
                die($e->getMessage());
            }
            $data = $form->getData();

            $dataRoles = $data["AdminType"];
            
            if(in_array(0, $dataRoles)){
                $userRole = new UsersRoles();
                $userRole->setUserId($id);
                $userRole->setRoleId(0);
                $em->persist($userRole);
    
                try{
                    $em->flush();
                }catch ( DBALException  $e ){
                    $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                    $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $id, 'users_roles', $e->getMessage(), $this->getIpAddress());
                    die($e->getMessage());
                }
            }

            if(in_array(1, $dataRoles)){
                $userRole = new UsersRoles();
                $userRole->setUserId($id);
                $userRole->setRoleId(1);
                $em->persist($userRole);
    
                try{
                    $em->flush();
                }catch ( DBALException  $e ){
                    $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                    $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $id, 'users_roles', $e->getMessage(), $this->getIpAddress());
                    die($e->getMessage());
                }
            }

            $test->setEmail($userEmail);
    
            try{
                $em->flush();
            }catch ( DBALException  $e ){
                $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $id, 'admin_users', $e->getMessage(), $request->getClientIp());
                die($e->getMessage());
            }
            
            $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
            $systemLogRepo->addSystemLog($user->getAdminId(), $id, 'admin_users', 'Edit', $request->getClientIp());
    
            return $this->render('Admin/ManageAdmins/UpdateAdmin.html.twig',[
                'form'=> $form->createView(),
                'tests'=>$test,
                'message'=>$message,
                'pageNumber'=>$pageNumber,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userPages' => $systemPages,
            ]);
        }
    
        try{
            $em->flush();
        }catch ( DBALException  $e ){
            die($e->getMessage());
        }
        return $this->render('Admin/ManageAdmins/UpdateAdmin.html.twig',[
                'form'=> $form->createView(),
                'tests' => $test,
                'pageNumber'=>$pageNumber,
                'language'=>$language,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userPages' => $systemPages,
            ]
        );
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

    public function getUserRoles($id){
        $repositoryUserRoles = $this->getDoctrine()->getManager()->getRepository(UsersRoles::class);

        return $repositoryUserRoles->getRolesById($id);
    }

    public function isInRoles($userId, $roleID){
        $repositoryUserRoles = $this->getDoctrine()->getManager()->getRepository(UsersRoles::class);

        $userRoles = $repositoryUserRoles->getRolesById($userId);

        return in_array($roleID, $userRoles);
    }

    public function createFormAdmin($id){
        $repositoryRoles = $this->getDoctrine()->getManager()->getRepository(Role::class);
        $repositoryUserRoles = $this->getDoctrine()->getManager()->getRepository(UsersRoles::class);

        $roles = $repositoryRoles->findAll();

        $rolesDisplay = Array();

        $defaultRoles = $repositoryUserRoles->getRolesById($id);

        foreach ($roles as $role){
            $rolesDisplay[$role->getRoleName()] = (String) $role->getRoleId();
        }

        $form = $this->createFormBuilder()
            ->add('Email',TextType::class)
            ->add('AdminType',ChoiceType::class,[
                'multiple' => true,
                'expanded' => true,
                'choices' => $rolesDisplay,
                'data' => $defaultRoles,
            ])
            ->add('Submit',SubmitType::class)
            ->getForm();

        return $form;
    }
}