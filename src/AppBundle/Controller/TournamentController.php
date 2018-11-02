<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Division;
use AppBundle\Entity\Tournament;
use AppBundle\Form\TournamentAddTeamsType;
use AppBundle\Tournament\Tables\DivisionABTable;
use AppBundle\Tournament\TournamentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TournamentController extends Controller
{
    /**
     * @Route("/test", name="test")
     */
    public function testAction(Request $request)
    {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find(1);
        $table = new DivisionABTable($tournament->getDivisionByName('A'));
        dump($table);
        die();
    }

    /**
     * @Route("/tournaments/list", name="list_tournaments")
     */
    public function listAction()
    {
        $tournaments = $this->getDoctrine()->getRepository(Tournament::class)->findBy([], ['id' => 'DESC']);
        return $this->render('tournaments/list.html.twig', ['tournaments' => $tournaments]);
    }

    /**
     * @Route("/tournaments/new", name="new_tournament")
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tournament = new Tournament();
        $em->persist($tournament);
        $em->flush();

        return $this->redirectToRoute('show_tournament', ['id' => $tournament->getId()]);
    }

    /**
     * @Route("/tournaments/show/{id}", name="show_tournament")
     */
    public function showAction($id)
    {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);

        if (null === $tournament) {
            throw new NotFoundHttpException();
        }

        $divisionATable = null;
        $divisionBTable = null;

        if ($tournament->getState() > Tournament::STATE_PICKED_TEAMS) {
            $divisionATable = new DivisionABTable($tournament->getDivisionByName(Division::NAME_A));
            $divisionBTable = new DivisionABTable($tournament->getDivisionByName(Division::NAME_B));
        }

        return $this->render('tournaments/show.html.twig', [
            "tournament" => $tournament,
            "divisionATable" => $divisionATable,
            "divisionBTable" => $divisionBTable
        ]);
    }

    /**
     * @Route("/tournaments/add_teams/{id}", name="add_teams_tournament")
     */
    public function addTeamsAction($id, Request $request)
    {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);

        if (null === $tournament) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(TournamentAddTeamsType::class, $tournament);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $tournament->setState(Tournament::STATE_PICKED_TEAMS);
            $em->persist($tournament);
            $em->flush();

            return $this->redirectToRoute('show_tournament', ['id' => $tournament->getId()]);
        }

        return $this->render('tournaments/add_teams.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/tournaments/start/{id}", name="start_tournament")
     */
    public function startTournamentAction($id, TournamentManager $tournamentManager)
    {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);

        if (null === $tournament) {
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();
        $tournamentManager->startTornament($tournament);

        $divisions = $tournament->getDivisions();
        foreach ($divisions as $division) {
            foreach ($division->getMatchResults() as $matchResult) {
                $em->persist($matchResult);
            }
            $em->persist($division);
        }

        $em->persist($tournament);
        $em->flush();
        return $this->redirectToRoute('show_tournament', ['id' => $tournament->getId()]);
    }
}