<?php

namespace App\Controller;

use App\Entity\Section;
use App\Form\SectionType;
use App\Repository\SectionRepository;
use App\Service\ImageUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app/section", name="app_section_")
 */
final class SectionController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param SectionRepository $sectionRepository
     * @return Response
     */
    public function index(SectionRepository $sectionRepository): Response
    {
        return $this->render('section/index.html.twig', [
            'sections' => $sectionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @param Request          $request
     * @param ImageUploader $imageUploader
     * @return Response
     */
    public function new(Request $request, ImageUploader $imageUploader): Response
    {
        $section = new Section();
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageFilename = $imageUploader->upload(
                    $imageFile,
                    $this->getParameter('uploads')['section_images']
                );
                $section->setImageFilename($imageFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($section);
            $entityManager->flush();

            return $this->redirectToRoute('app_section_index');
        }

        return $this->render('section/new.html.twig', [
            'section' => $section,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param Section $section
     * @return Response
     */
    public function show(Section $section): Response
    {
        return $this->render('section/show.html.twig', [
            'section' => $section,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request       $request
     * @param Section       $section
     * @param ImageUploader $imageUploader
     * @return Response
     */
    public function edit(Request $request, Section $section, ImageUploader $imageUploader): Response
    {
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageFilename = $imageUploader->upload(
                    $imageFile,
                    $this->getParameter('uploads')['section_images'],
                    $section->getImageFilename()
                );
                $section->setImageFilename($imageFilename);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_section_index');
        }

        return $this->render('section/edit.html.twig', [
            'section' => $section,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request       $request
     * @param Section       $section
     * @param ImageUploader $imageUploader
     * @return Response
     */
    public function delete(Request $request, Section $section, ImageUploader $imageUploader): Response
    {
        if ($this->isCsrfTokenValid('delete'.$section->getId(), $request->request->get('_token'))) {
            if ($section->getImageFilename() !== null) {
                $imageUploader->remove(
                    $section->getImageFilename(),
                    $this->getParameter('uploads')['section_images']
                );
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($section);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_section_index');
    }

    /**
     * @Route("/img/{id}", name="delete_img", methods={"DELETE"})
     * @param Request       $request
     * @param Section       $section
     * @param ImageUploader $imageUploader
     * @return Response
     */
    public function deleteImage(Request $request, Section $section, ImageUploader $imageUploader): Response
    {
        if ($this->isCsrfTokenValid('delete_image'.$section->getId(), $request->request->get('_token'))) {
            if ($section->getImageFilename() !== null) {
                $imageUploader->remove(
                    $section->getImageFilename(),
                    $this->getParameter('uploads')['section_images']
                );

                $section->setImageFilename(null);
            }

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('app_section_edit', [
            'id' => $section->getId()
        ]);
    }
}
