<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 12/31/2017
 * Time: 10:19 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\ProjectMembers;
use AppBundle\Entity\Projects;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class EditProjectController extends Controller{
    /**
     * @Route("/Admin/ManageProjects/EditProject/{id}",name="EditProject")
     */
    public function AddAdminAction(Request $request, $id){
        $session = new Session();

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        $PAGE_ID = 3;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        if($permissions->getCanEdit() == 0){
            die("You cannot enter this page.");
        }
    
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());

        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();

        $form = $this->createFormProject($id);

        $form->handleRequest($request);

        $pageNumber = $session->get('pageNumber');

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();

            $project = $this->getDoctrine()->getRepository('AppBundle:Projects')->findProjectById($id)[0];
            $repositoryProjectMembers =  $this->getDoctrine()->getRepository('AppBundle:ProjectMembers');
            $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');

            $project->setProjectName($data['projectName']);
            $project->setHeadOfProject($userRepository->findUserIdByName($data["headOfProject"]));
            $project->setDateOfProject($data['dateOfProject']);
            $project->setDescription($data['description']);
            $project->setIsDeleted(0);

            $em->persist($project);

            try{
                $em->flush();
            }catch ( DBALException  $e ){
                $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $project->getProjectID(), 'projects', $e->getMessage(), $request->getClientIp());
                die($e->getMessage());
            }

            $repositoryProjectMembers->removeMembersForProject($id);
            for($i = 0; $i < sizeof($data["members"]); $i++){
                $member = new ProjectMembers();
                $member->setProjectId($project->getProjectID());
                $member->setUserId($data["members"][$i]);

                $em->persist($member);
            }

            try{
                $em->flush();
            }catch ( DBALException  $e ){
                $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $project->getProjectID(), 'projects', $e->getMessage(), $request->getClientIp());
                die($e->getMessage());
            }

            return $this->redirectToRoute('ManageProjects', [
                'pageNumber' => 1,
            ]);
        }

        return $this->render('Admin/ManageProjects/EditProject.html.twig',[
                'form'=> $form->createView(),
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

    public function createFormProject($id){
        $repositoryUsers = $this->getDoctrine()->getRepository('AppBundle:User');
        $repositoryProjectMembers =  $this->getDoctrine()->getRepository('AppBundle:ProjectMembers');

        $users = $repositoryUsers->getAllUsers();
        $members = $repositoryProjectMembers->findMembersIdByProjectId($id);

        $arrayUsers = array();

        for($i = 0; $i < sizeof($users); $i++){
            $user = [
                $users[$i]['first_name']." ".$users[$i]["last_name"] => $users[$i]["user_id"]
            ];

            array_push($arrayUsers, $user);
        }

        $project = $this->getDoctrine()->getRepository('AppBundle:Projects')->findProjectById($id)[0];
        dump($members);
        $form = $this->createFormBuilder()
            ->add('projectName', TextType::class, [
                'data' => $project->getProjectName(),
            ])
            ->add('headOfProject', TextType::class, [
                'data' => "IDk"
            ])
            ->add('dateOfProject',DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' =>['class' => 'js-datepicker',
                ],
                'data' => $project->getDateOfProject(),
            ])
            ->add('description', TextType::class, [
                'data' => $project->getDescription(),
            ]) ->add('members', ChoiceType::class,[
                'choices' => $arrayUsers,
                'multiple' => true,
                'expanded' => true,
                'data' => $members,
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