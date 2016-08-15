<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Courier;
use AppBundle\Form\CreateCourierForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CourierController
 * @Route("/couriers")
 */
class CourierController extends Controller
{
    /**
     * @Route("/", name="courier_list")
     * @Method("GET")
     * @Template("@App/Courier/list.html.twig")
     * @param Request $request
     * @return array|Response
     */
    public function indexAction(Request $request)
    {
        $couriers = $this->getDoctrine()->getRepository('AppBundle:Courier')->findAll();

        return [
            'couriers' => $couriers,
        ];
    }

    /**
     * @Route("/create/", name="courier_create")
     * @Method({"GET","POST"})
     * @Template("@App/Courier/create.html.twig")
     * @param Request $request
     * @return array|Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(CreateCourierForm::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var Courier $courier */
            $courier = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($courier);
            $em->flush();

            $this->addFlash('success', 'Курьер ' . $courier->getFullName() . ' успешно добавлен.');

            return $this->redirectToRoute('courier_list');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/get_courier_busy_dates", name="get_courier_busy_dates")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function getCourierBusyDates(Request $request)
    {
        $courierId = $request->get('courierId');
        $courierBusyDays = $this->get('app.service.schedule_service')->getCourierBusyDates($courierId);

        return new JsonResponse($courierBusyDays);
    }
}
