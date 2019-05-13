<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 12/31/2017
 * Time: 10:19 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\admin;
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

class AddUserController extends Controller{
    /**
     * @Route("/Admin/ManageUsers/AddUser",name="AddUser")
     */
    public function AddUserAction(Request $request){
        $session = new Session();

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        $PAGE_ID = 4;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        if($permissions->getCanAdd() == 0){
            die("You cannot enter this page.");
        }
    
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());

        $pageNumber = $session->get('pageNumber');

        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();

        $language = $session->get('locale');
        $request->setLocale($language);
        $form = $this->createFormAdmin();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();

            $newUser = new User();
            $newUser->setFirstName($data['firstName']);
            $newUser->setLastName($data['lastName']);
            $newUser->setUserEmail($data['email']);
            $newUser->setPlainedPassword($data['plainPassword']);
            $password = $this->get('security.password_encoder')->encodePassword($newUser, $data['plainPassword']);
            $newUser->setPassword($password);
            $newUser->setBirthDate($data['birthday']);
            $newUser->setIsDeleted(0);
            $newUser->setStatusId(1);
            $newUser->setPositionId($data['position']);
            $newUser->setCountryId($data['country']);
            $newUser->setRegistrationDate($this->getDate());
            $em->persist($newUser);
            
            try{
                $em->flush();
            }catch ( DBALException  $e ){
                $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $newUser->getUserId(), 'users', $e->getMessage(), $request->getClientIp());
                die($e->getMessage());
            }
            
            $userId = $newUser->getUserId();
    
            $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
            $systemLogRepo->addSystemLog($user->getAdminId(), $userId, 'admin_users', 'Add', $request->getClientIp());

            return $this->redirectToRoute('Users',array(
                'language' => $language,
                'pageNumber'=>$pageNumber,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userPages' => $systemPages,
            ));
        }

        return $this->render('Admin/ManageUsers/AddUser.html.twig',[
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

    public function createFormAdmin(){
        $repositoryPositions = $this->getDoctrine()->getManager()->getRepository(Positions::class);
        $repositoryCountries =  $this->getDoctrine()->getManager()->getRepository(Country::class);

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

        $form = $this->createFormBuilder()
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('country', ChoiceType::class, [
                'choices' => $countriesArray,
            ])
            ->add('plainPassword', RepeatedType::class,[
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password',
                ],
                'second_options' => [
                    'label' => 'Repeat Password'
                ]
            ])
            ->add('position', ChoiceType::class, [
                'choices' => $positionsArray,
            ])
            ->add('birthday',DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' =>['class' => 'js-datepicker',
                ],
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