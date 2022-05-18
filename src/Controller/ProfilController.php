<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Participant;
use App\Form\ArticleType;
use App\Form\ParticipantType;
use App\Repository\ArticleRepository;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilController extends AbstractController
{
    /**
     * * Affichage du profil
     * @Route("/profil/{id}", name="profil")
     */
    public function profil(int $id, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);

        if(!$participant){
            throw $this->createNotFoundException('Profil inexistant');
        }
        return $this->render('profil/index.html.twig', [
            "participant"=>$participant
        ]);
    }

    /**
     * Modification du profil
     *
     * @Route("/editer/{id}", name="profil_editer", methods={"GET","POST"})
     */
    public function editer(Request $request, Participant $participant, ParticipantRepository $participantRepository, SluggerInterface $slugger): Response
    {
        $profilForm = $this->createForm(ParticipantType::class, $participant);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            $photoFile = $profilForm->get('photo')->getData();
            // prise en charge de l'image
            if ($photoFile) {
                $photoName = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($photoName);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                // on deplace l'image vers le dossier photos
                // photos_directory est defini dans config/service.yaml
                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }
                // on hydrate le nom de l'image dans le participant
                $participant->setPhoto($newFilename);
            }

            $participantRepository->add($participant, true);
            $this->addFlash('success', 'Profil modifié.');

            return $this->redirectToRoute('profil', ['id'=>$participant->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profil/editer.html.twig', [
            'participant' => $participant,
            'profilForm' => $profilForm,
        ]);
    }


}
