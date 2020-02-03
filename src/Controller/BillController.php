<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Bill;
use App\Form\BillType;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of BillController
 *
 * @author mateusz
 */
class BillController extends AbstractController {
    
    /**
     * Lista paragonów
     * 
     * @Route("/")
     */
    public function index () {
        return $this->render('bill/index.html.twig', []);
    }
    
    /**
     * Nowy paragon
     * 
     * @Route("/new")
     */
    public function newBill (Request $request) {
        
        $entityManager = $this->getDoctrine()->getManager();

        $bill = new Bill();
        $d = new \DateTime();
        $d->format('Y-m-d');
        $bill->setDate($d);
        $bill->setSummaryBrutto(0);
        $bill->setSummaryNetto(0);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($bill);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        
        // success
        $this->addFlash(
            'success',
            'Utworzono nowy paragon!'
        );
        return $this->redirect('/edit/'.$bill->getId());
    }
    
    /**
     * Edycja paragonu
     * 
     * @Route("/edit/{id}")
     */
    public function editBill (Request $request, $id) {
        $bill = $this->getDoctrine()
            ->getRepository(Bill::class)
            ->find($id);
                
        $originalPositions = new ArrayCollection();
        foreach ($bill->getPositions() as $position) {
            $originalPositions->add($position);
        }
        
        $form = $this->createForm(BillType::class, $bill);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $bill = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            foreach ($originalPositions as $position) {
                if (false === $bill->getPositions()->contains($position)) {
                    $bill->removePosition($position);
                    $entityManager->persist($position);
                }
            }
            
            $entityManager->persist($bill);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Zapisano paragon!'
            );
        }
        
        return $this->render('bill/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
