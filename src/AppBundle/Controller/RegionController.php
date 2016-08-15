<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Region;
use AppBundle\Form\CreateRegionForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RegionController
 * @Route("/regions")
 */
class RegionController extends Controller
{
    /**
     * @Route("/", name="region_list")
     * @Method("GET")
     * @Template("@App/Region/list.html.twig")
     * @param Request $request
     * @return array|Response
     */
    public function indexAction(Request $request)
    {
        $regions = $this->getDoctrine()->getRepository('AppBundle:Region')->findAll();

        return [
            'regions' => $regions,
        ];
    }

    /**
     * @Route("/create/", name="region_create")
     * @Method({"GET","POST"})
     * @Template("@App/Region/create.html.twig")
     * @param Request $request
     * @return array|Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(CreateRegionForm::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var Region $region */
            $region = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($region);
            $em->flush();

            $this->addFlash('success', 'Регион ' . $region->getName() . ' успешно добавлен.');

            return $this->redirectToRoute('region_list');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/get_region_travel_time", name="get_region_travel_time")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function getRegionTravelTime(Request $request)
    {
        $regionTravelTime = $this->getDoctrine()->getRepository('AppBundle:Region')->getRegionTravelTime($request->get('regionId'));

        return new JsonResponse($regionTravelTime);
    }
}
