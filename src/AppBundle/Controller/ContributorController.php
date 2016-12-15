<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contributor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contributor controller.
 *
 * @Route("admin/contributors")
 */
class ContributorController extends Controller
{
    /**
     * Lists all contributor entities.
     *
     * @Route("/", name="contributor_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $contributors = $em->getRepository('AppBundle:Contributor')->findBy([], null, 100);

        return $this->render('contributor/index.html.twig', array(
            'contributors' => $contributors,
        ));
    }

    /**
     * Creates a new contributor entity.
     *
     * @Route("/new", name="contributor_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $contributor = new Contributor();
        $form = $this->createForm('AppBundle\Form\ContributorType', $contributor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contributor);
            $em->flush($contributor);

            return $this->redirectToRoute('contributor_show', array('id' => $contributor->getId()));
        }

        return $this->render('contributor/new.html.twig', array(
            'contributor' => $contributor,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a contributor entity.
     *
     * @Route("/{id}", name="contributor_show")
     * @Method("GET")
     */
    public function showAction(Contributor $contributor, $_format, $_locale)
    {
        $deleteForm = $this->createDeleteForm($contributor);

        return $this->render('contributor/show.html.twig', array(
            'contributor' => $contributor,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing contributor entity.
     *
     * @Route("/{id}/edit", name="contributor_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Contributor $contributor)
    {
        $deleteForm = $this->createDeleteForm($contributor);
        $editForm = $this->createForm('AppBundle\Form\ContributorType', $contributor);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contributor_edit', array('id' => $contributor->getId()));
        }

        return $this->render('contributor/edit.html.twig', array(
            'contributor' => $contributor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a contributor entity.
     *
     * @Route("/{id}", name="contributor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Contributor $contributor)
    {
        $form = $this->createDeleteForm($contributor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contributor);
            $em->flush($contributor);
        }

        return $this->redirectToRoute('contributor_index');
    }

    /**
     * Creates a form to delete a contributor entity.
     *
     * @param Contributor $contributor The contributor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Contributor $contributor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contributor_delete', array('id' => $contributor->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
