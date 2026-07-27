<?php

namespace Plugin\SamplePlugin44\Controller\Admin;

use Eccube\Controller\AbstractController;
use Plugin\SamplePlugin44\Form\Type\Admin\ConfigType;
use Plugin\SamplePlugin44\Repository\ConfigRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AbstractController
{
    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * ConfigController constructor.
     *
     * @param ConfigRepository $configRepository
     */
    public function __construct(ConfigRepository $configRepository)
    {
        $this->configRepository = $configRepository;
    }

     #[Route(path: '/%eccube_admin_route%/sample_plugin44/config', name: 'sample_plugin44_admin_config', methods: ['GET', 'POST'])]
     #[Template("@SamplePlugin44/admin/config.twig")]
    public function index(Request $request)
    {
        $Config = $this->configRepository->get();
        $form = $this->createForm(ConfigType::class, $Config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Config = $form->getData();
            $this->entityManager->persist($Config);
            $this->entityManager->flush();
            $this->addSuccess('登録しました。', 'admin');

            return $this->redirectToRoute('sample_plugin44_admin_config');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
