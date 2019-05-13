<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 12/31/2017
 * Time: 10:19 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Country;
use AppBundle\Entity\Positions;
use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class EditUserController extends Controller{
    /**
     * @Route("/Admin/ManageUsers/EditUser/{id}",name="EditUser")
     */
    public function AddUserAction(Request $request, $id){
        $session = new Session();

        $admin = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($admin->getAdminID())[0];

        $PAGE_ID = 4;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        if($permissions->getCanAdd() == 0){
            die("You cannot enter this page.");
        }

        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($admin->getAdminID());

        $pageNumber = $session->get('pageNumber');

        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();

        $language = $session->get('locale');
        $request->setLocale($language);
        $form = $this->createFormUser($id);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $userRepo = $em->getRepository('AppBundle:User');

            $user = $userRepo->findUserById($id);

            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setUserEmail($data['email']);
            $user->setBirthDate($data['birthday']);
            $user->setPositionId($data['position']);
            $user->setCountryId($data['country']);
            $user->setRegistrationDate($this->getDate());
            $em->persist($user);

            try{
                $em->flush();
            }catch ( DBALException  $e ){
                $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                $technicalIssuesRepo->addTechnicalIssue($admin->getAdminId(), $user->getUserId(), 'users', $e->getMessage(), $request->getClientIp());
                die($e->getMessage());
            }

            $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
            $systemLogRepo->addSystemLog($admin->getAdminId(), $id, 'admin_users', 'Add', $request->getClientIp());

            return $this->redirectToRoute('EditUser',array(
                'language' => $language,
                'pageNumber'=>$pageNumber,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userPages' => $systemPages,
                'message' => "Update Successful",
                'id' => $id,
            ));
        }

        return $this->render('Admin/ManageUsers/EditUser.html.twig',[
                'form'=> $form->createView(),
                'language' => $language,
                'pageNumber'=>$pageNumber,
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

    public function createFormUser($id){
        $repositoryPositions = $this->getDoctrine()->getManager()->getRepository(Positions::class);
        $repositoryCountries =  $this->getDoctrine()->getManager()->getRepository(Country::class);
        $repositoryUsers =  $this->getDoctrine()->getManager()->getRepository(User::class);

        $positions = $repositoryPositions->findPositions();
        $countries = $repositoryCountries->findAllCountries();

        $positionsArray = array();
        foreach($positions as $value){
            $positionsArray[$value->getPositionName()] = $value->getPositionId();
        }

        $countriesArray = array();
        foreach($countries as $value){
            $countriesArray[$value->getCountryName()] = $value->getCountryId();
        }

        $user = $repositoryUsers->findUserById($id);

        $form = $this->createFormBuilder()
            ->add('firstName', TextType::class, [
                'data' => $user->getFirstName(),
            ])
            ->add('lastName', TextType::class, [
                'data' => $user->getLastName(),
            ])
            ->add('email', EmailType::class, [
                'data' => $user->getUserEmail(),
            ])
            ->add('country', ChoiceType::class, [
                'choices' => $countriesArray,
                'data' => $user->getCountryId(),
            ])
            ->add('position', ChoiceType::class, [
                'choices' => $positionsArray,
                'data' => $user->getPositionId(),
            ])
            ->add('birthday',DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' =>['class' => 'js-datepicker',
                ],
                'data' => $user->getBirthDate(),
            ])
            ->add('Submit',SubmitType::class)
            ->add('Clear', ResetType::class)
            ->getForm();

        return $form;
    }

    public function getDate(){
        return new \DateTime('now', (new \DateTimeZone('Asia/Beirut')));
    }
}