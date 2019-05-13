<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 2/6/2018
 * Time: 8:38 PM
 */

namespace AppBundle\Controller;


use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageProjects extends Controller {
    /**
     * @Route("Admin/ManageProjects/{pageNumber}", name="ManageProjects")
     */
    public function manageProjectsActions(Request $request, $pageNumber){
        $session = new Session();

        $PAGE_ID = 3;
        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());

        $projectsRepo = $this->getDoctrine()->getRepository('AppBundle:Projects');
        $projects = $projectsRepo->findProjectsByPage($pageNumber, 8);

        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();

        $session->set('pageNumber', $pageNumber);
        $session->set('systemPages', $systemPages);

        return $this->render('Admin/ManageProjects/ManageProjects.html.twig', [
            'pageNumber' => $pageNumber,
            'numPages' => 1,
            'userPages' => $systemPages,
            'canAdd' => $canAdd,
            'canView' => $canView,
            'canDelete' => $canDelete,
            'canEdit' => $canEdit,
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/Admin/ManageProjects/delete/{id}" , name="DeleteProject")
     */
    public function DeleteProject(Request $request, $id){
        $session = new Session();

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        $pageNumber = $session->get('pageNumber');

        if($permissions->getCanDelete() == 0){
            die("You cannot enter this page.");
        }

        $em = $this->getDoctrine()->getManager();
        $DeleteProject = $em->getRepository('AppBundle:Projects')->find($id);
        
        if (!$DeleteProject) {
            $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
            $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'unknown', 'projects', "Project not found.", $this->getIpAddress());
            
            return $this->redirectToRoute('ManageProjects',array(
                'pageNumber'=>$pageNumber,
            ));
        }
        
        $DeleteProject->setIsDeleted(1);
        $em->flush();
    
        $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
        $systemLogRepo->addSystemLog($user->getAdminId(), $id, 'Projects', 'Delete', $request->getClientIp());
    
        return $this->redirectToRoute('ManageProjects',array(
            'pageNumber'=>$pageNumber,
        ));
    }

    /**
     * @Route("/Admin/ManageProjects/display/{id}", name="DisplayProject")
     */
    public function display(Request $request, $id){
        $session = new Session();

        $admin = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($admin->getAdminID())[0];

        if($permissions->getCanView() == 0){
            die("You cannot enter this page.");
        }

        $systemPages = $session->get('systemPages');

        $project = $this->getDoctrine()->getRepository('AppBundle:Projects')->findProjectByIdView($id);

        if (!$project) {
            $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
            $technicalIssuesRepo->addTechnicalIssue($admin->getAdminId(), 'unknown', 'projects', "Project not found.", $request->getClientIp());

            return $this->redirectToRoute('ManageProjects',array(
                'pageNumber'=> 1,
            ));
        }

        $pageNumber = $session->get('pageNumber');

        $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
        $systemLogRepo->addSystemLog($admin->getAdminID(), $id, 'users', 'Display', $request->getClientIp());

        $members = $this->getDoctrine()->getRepository('AppBundle:ProjectMembers')->findMembersByProjectId($id);

        $arrayMembers = array();

        for($i = 0; $i < sizeof($members); $i++){
            array_push($arrayMembers, $members[$i]["first_name"]." ".$members[$i]["last_name"]);
        }

        dump($arrayMembers);
        dump($systemPages);
        return $this->render('Admin/ManageProjects/ViewProject.html.twig', [
                'project' => $project,
                'members' => $arrayMembers,
                'userPages' => $systemPages,
                'pageNumber' => $pageNumber,
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
}