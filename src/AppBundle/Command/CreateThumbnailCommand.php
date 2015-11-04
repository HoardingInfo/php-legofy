<?php
namespace AppBundle\Command;

use AppBundle\Entity\Image;
use Doctrine\ORM\EntityRepository;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use Imagine\Exception\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateThumbnailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('generate:thumbnail')
            ->setDescription('Generate missing thumbnails')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('doctrine')
            ->getRepository('AppBundle:Image');

        /** @var Image[] $images */
        $images = $repository->findAll();
        foreach ($images as $image) {
            $thumbnailFile = sprintf('%s/../../../web/images/thumbnails/%s', __DIR__, $image->getName());
            if (file_exists($thumbnailFile)) {
                $output->writeln('Thumbnail already generated');
                continue;
            }

            $imagine = new Imagine();
            try {
                $imageFile = $imagine->open(sprintf('%s/../../../web/images/%s', __DIR__, $image->getName()));
            } catch (InvalidArgumentException $e) {
                $output->writeln($e->getMessage());
                continue;
            }
            $box = $imageFile->getSize();
            if ($box->getHeight() > $box->getWidth()) {
                $imageFile->resize(new Box(400, ($box->getHeight() * (400 / $box->getWidth()))))
                    ->crop(new Point(0, 0), new Box(400, 400));
            } else {
                $newWidth = $box->getWidth() * (400 / $box->getHeight());
                $imageFile->resize(new Box($newWidth, 400))
                    ->crop(new Point(($newWidth - 400) / 2, 0), new Box(400, 400));
            }

            if ($imageFile->save($thumbnailFile)) {
                $output->writeln('Thumbnail generated');
            }


        }
    }
}