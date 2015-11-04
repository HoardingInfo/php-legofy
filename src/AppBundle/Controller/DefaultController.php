<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return new Response($this->renderView('AppBundle:Default:index.html.twig', ['menuActive' => 'home']));
    }

    /**
     * @Route("/file-upload", name="uploadFile")
     * @Template()
     */
    public function uploadFileAction(Request $request)
    {
        $allow = $request->get('allow', false);
        /** @var File $file */
        $file = $request->files->get('file');
        $imageName = uniqid('legofy-online').'.png';
        $command = sprintf('legofy %s/%s %s/../../../web/images/%s', $file->getPath(), $file->getFilename(), __DIR__, $imageName);
        $process = new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $image = new Image();
        $image->setPrivate(false)
            ->setName($imageName)
            ->setCreationDate(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();

        return [
            'image' => $image
        ];
    }

    /**
     * @Route("/gallery/{page}", name="gallery")
     * @Template()
     */
    public function galleryAction($page = 1)
    {
        $offset = 20 * ($page-1);
        $prev = $page - 1;
        $next = $page + 1;
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Image');
        $images = $repository->findBy(['private' => false], ['creationDate' => 'DESC'], 20, $offset);
        return ['menuActive' => 'gallery', 'images' => $images, 'page' => $page, 'prev' => $prev, 'next' => $next];
    }

    /**
     * @Route("/image/{id}/{name}", name="image", requirements={"id" = "\d+"})
     * @Template()
     */
    public function imageAction($id, $name = null)
    {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Image');
        $image = $repository->find($id);
        return ['menuActive' => '', 'image' => $image];
    }
}
