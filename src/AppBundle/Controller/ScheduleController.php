<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Schedule;
use AppBundle\Form\AddScheduleItemForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CourierController
 * @Route("/schedule")
 */
class ScheduleController extends Controller
{
    /**
     * @Route("/", name="schedule_list")
     * @Method("GET")
     * @Template("@App/Schedule/list.html.twig")
     * @param Request $request
     * @return array|Response
     */
    public function listAction(Request $request)
    {
        $schedule = $this->getDoctrine()->getRepository('AppBundle:Schedule')->findAll();

        return [
            'schedule' => $schedule,
        ];
    }

    /**
     * @Route("/create/", name="schedule_create")
     * @Method({"GET","POST"})
     * @Template("@App/Schedule/create.html.twig")
     * @param Request $request
     * @return array|Response
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(AddScheduleItemForm::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var Schedule $scheduleItem */
            $scheduleItem = $form->getData();
            $em->persist($scheduleItem);
            $em->flush();

            $this->addFlash('success', 'Новая поездка создана.');

            return $this->redirectToRoute('schedule_list');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/get_schedule_list/", name="get_schedule_list")
     * @Method({"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getScheduleListAction(Request $request)
    {
        $order = $request->get('order')[0];
        $columnsParams = $request->get('columns');
        $start = $request->get('start');
        $length = $request->get('length');
        $extraSearch = $request->get('extra_search');

        $data = $this->getDoctrine()->getRepository('AppBundle:Schedule')->getFilteredScheduleList($start, $length, $columnsParams, $order, $extraSearch);
        $iTotalRecords = $this->getDoctrine()->getRepository('AppBundle:Schedule')->getScheduleListCount();
        $iTotalDisplayRecords = $this->getDoctrine()->getRepository('AppBundle:Schedule')->getFilteredScheduleListCount($extraSearch);

        $output = [
            "iTotalRecords"        => (int) $iTotalRecords,
            "iTotalDisplayRecords" => (int) $iTotalDisplayRecords,
            "data"                 => $data,
        ];

        return new JsonResponse($output);
    }
}
