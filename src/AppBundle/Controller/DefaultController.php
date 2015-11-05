<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
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

        $imagine = new Imagine();
        $imageFile = $imagine->open(sprintf('%s/../../../web/images/%s', __DIR__, $imageName));
        $box = $imageFile->getSize();
        if ($box->getHeight() > $box->getWidth()) {
            $imageFile->resize(new Box(400, ($box->getHeight() * (400 / $box->getWidth()))))
                ->crop(new Point(0, 0), new Box(400, 400));
        } else {
            $newWidth = $box->getWidth() * (400 / $box->getHeight());
            $imageFile->resize(new Box($newWidth, 400))
                ->crop(new Point(($newWidth - 400) / 2, 0), new Box(400, 400));
        }

        $imageFile->save(sprintf('%s/../../../web/images/thumbnails/%s', __DIR__, $imageName));

        $image = new Image();
        $image->setPrivate($allow ? false : true)
            ->setName($imageName)
            ->setCreationDate(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();

        return new JsonResponse([
            'url' => $this->generateUrl('image', ['id' => $image->getId(), 'name' => $image->getName()])
        ]);
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

    /**
     * @Route("/videos-legofy", name="videos-legofy")
     * @Template()
     */
    public function videosAction()
    {
        return ['menuActive' => 'videos'];
    }
}
