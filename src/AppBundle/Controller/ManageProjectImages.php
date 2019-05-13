<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 12/31/2017
 * Time: 10:19 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Images;
use AppBundle\Entity\Projects;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageProjectImages extends Controller{
    /**
     * @Route("/Admin/ManageProjects/ManageImages/{id}",name="ProjectImages")
     */
    public function ProjectImagesAction(Request $request, $id){
        $session = new Session();

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        $PAGE_ID = 3;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        if($permissions->getCanAdd() == 0){
            die("You cannot enter this page.");
        }

        $session->set('page', 1);
        $session->set('id', $id);

        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());

        $projectImagesRepo = $this->getDoctrine()->getRepository('AppBundle:Images');
        $projectImages = $projectImagesRepo->findProjectImages($id, 1, 8);

        $more = 0;
        if($projectImagesRepo->findProjectImages($id, 2, 8)){
            $more = 1;
        }

        return $this->render('Admin/ManageProjects/ProjectImages.html.twig',[
                'userPages' => $systemPages,
                'images' => $projectImages,
                'more' => $more,
                'project_id' => $id,
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

    public function getDate(){
        return new \DateTime('now', (new \DateTimeZone('Asia/Beirut')));
    }

    /**
     * @Route("/Admin/ManageProjects/ManageImages/Project/getImages", name="getMoreProjectImages")
     */
    public function getMoreImages(Request $request){
        if($request->isXmlHttpRequest()) {
            $session = new Session();

            $page = $session->get('page') + 1;
            $session->set('page', $page);
            $id = $session->get('id');

            $projectImagesRepo = $this->getDoctrine()->getRepository('AppBundle:Images');
            $projectImages = $projectImagesRepo->findProjectImages($id, $page, 8);

            return new JsonResponse($projectImages);
        }

        return new Response(null);
    }

    /**
     * @Route("/Admin/ManageProjects/ManageImages/Project/getImagesCount", name="getProjectImagesCount")
     */
    public function getImagesCount(Request $request){
        if($request->isXmlHttpRequest()) {
            $session = new Session();

            $id = $session->get('id');

            $projectImagesRepo = $this->getDoctrine()->getRepository('AppBundle:Images');
            $projectImagesCount = $projectImagesRepo->findNumberOfImages($id);

            return new JsonResponse($projectImagesCount);
        }

        return new Response(null);
    }

    /**
     * @Route("/Admin/ManageProjects/ManageImages/Project/uploadImageThumbController", name="uploadThumbnailController")
     */
    public function uploadThumbnailImage(Request $request){
        if($request->isXmlHttpRequest()) {

            file_put_contents('C:\Users\Ali Fakhoury\Desktop\\thumbnail'.$_FILES['file']['name'], file_get_contents($_FILES['file']['tmp_name']));

            return new JsonResponse($_FILES);
        }

        return new Response(null);
    }

    /**
     * @Route("/Admin/ManageProjects/ManageImages/Project/uploadImageController", name="uploadNormalController")
     */
    public function uploadNormalImage(Request $request){
        if($request->isXmlHttpRequest()) {
            $session = new Session();


            $name = $session->get('formData');

            $thumbURL = "C:\Users\Ali Fakhoury\Desktop\\thumbnail".$name;
            $imageURL = "C:\Users\Ali Fakhoury\Desktop\\image".$name;
            $projectID = $session->get('projectID');

            $em = $this->getDoctrine()->getManager();

            $projectImage = new Images();

            $projectImage->setImageUrl($imageURL);
            $projectImage->setThumbnailUrl($thumbURL);
            $projectImage->setProjectID($projectID);
            $em->persist($projectImage);
            $em->flush();


            file_put_contents('C:\Users\Ali Fakhoury\Desktop\\normal'.$_FILES['file']['name'], file_get_contents($_FILES['file']['tmp_name']));
            return new JsonResponse($_FILES);
        }

        return new Response(null);
    }
}