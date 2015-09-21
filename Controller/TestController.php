<?php

namespace peerj\MarkdownBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/test/markdown", name="test_markdown")
     */
    public function testMarkdownAction(Request $request)
    {
        $entity = new \stdClass();
        $entity->title = 'The title';
        $entity->description = 'The description';

        $form = $this->createFormBuilder($entity)
            ->add('title', 'markdown', [
                'attr' => [
                    'data-single-line' => true
                ]
            ])
            ->add('description', 'markdown', [
                'attr' => [
                    'rows' => 10,
                ]
            ])
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            /*
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirectToRoute('test_markdown');
            */
        }

        return $this->render('peerjMarkdownBundle::test.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }
}
