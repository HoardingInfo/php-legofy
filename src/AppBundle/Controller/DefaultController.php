<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
        /** @var File $file */
        $file = $request->files->get('file');
        $command = sprintf('legofy %s/%s %s/../../../web/images/%s', $file->getPath(), $file->getFilename(), __DIR__, $file->getBasename());
        $process = new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return [
            'imageName' => $file->getBasename() . '.png'
        ];
    }

    /**
     * @Route("/gallery/{page}", name="gallery")
     * @Template()
     */
    public function galleryAction($page = 1)
    {
        return ['menuActive' => 'gallery'];
    }
}
