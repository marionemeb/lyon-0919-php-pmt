<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Trip;
use App\Form\Trip1Type;
use App\Repository\TripRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/trip")
 */
class TripController extends AbstractController
{
    /**
     * @Route("/", name="trip_index", methods={"GET"})
     * @param TripRepository $tripRepository
     * @return Response
     */
    public function index(TripRepository $tripRepository): Response
    {
        return $this->render('trip/index.html.twig', [
            'trips' => $tripRepository->findBy(array(), ['createdAt' => 'desc'])

        ]);
    }

    /**
     * @Route("/new", name="trip_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $trip = new Trip();
        $picture = new Picture();
        $form = $this->createForm(Trip1Type::class, $trip);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form['imageFile']->getData();
            $fileName = $this->generateUniqueFileName() . '.' . $pictureFile->guessExtension();
            $destination = $this->getParameter('pictures_directory');
            $pictureFile->move(
                $destination,
                $fileName
            );
            $picture->setName($fileName);
            $trip->setPicture($picture);

            $entityManager = $this->getDoctrine()->getManager();
            $trip->setCreatedAt(new DateTime('now'));
            $entityManager->persist($trip);
            $entityManager->flush();
            return $this->redirectToRoute('trip_index');
        }
        return $this->render('trip/new.html.twig', ['trip' => $trip,
            'form' => $form->createView(),]);
    }

    /**
     * @Route("/{id}", name="trip_show", methods={"GET"})
     * @param Trip $trip
     * @return Response
     */

    public function show(Trip $trip): Response
    {

        return $this->render('trip/show.html.twig', [
            'trip' => $trip,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="trip_edit", methods={"GET","POST"})
     */

    public function edit(Request $request, Trip $trip): Response
    {
        $form = $this->createForm(Trip1Type::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trip->setUpdatedAt(new DateTime('now'));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('trip_index');
        }

        return $this->render('trip/edit.html.twig', [
            'trip' => $trip,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trip_delete", methods={"DELETE"})
     */

    public function delete(Request $request, Trip $trip): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trip->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trip_index');
    }

    /**
     * @return string
     */

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
