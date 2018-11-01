<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Form\TeamFormType;
use AppBundle\Tournament\TeamGenerator;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TeamController extends Controller
{
    /**
     * @Route("/teams/list", name="list_teams")
     */
    public function listAction()
    {
        $teams = $this->getDoctrine()->getRepository(Team::class)->findBy([], ['id' => 'DESC']);
        return $this->render('teams/list.html.twig', ['teams' => $teams]);
    }

    /**
     * @Route("/teams/new", name="new_team")
     */
    public function newAction(Request $request)
    {
        $team = new Team();

        $form = $this->createForm(TeamFormType::class, $team);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();

            return $this->redirectToRoute('list_teams');
        }

        return $this->render('teams/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/teams/generate", name="generate_teams")
     */
    public function generateAction()
    {
        $teamGenerator = new TeamGenerator();
        $teamCollection = $teamGenerator->generate(20);
        $em = $this->getDoctrine()->getManager();
        foreach ($teamCollection as $team) {
            $em->persist($team);
        }
        $em->flush();

        return $this->redirectToRoute('list_teams');
    }
}