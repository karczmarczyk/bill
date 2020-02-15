<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Bill;
use App\Form\BillType;
use Doctrine\Common\Collections\ArrayCollection;
use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\ActionColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\DataTableFactory;

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
     * @Route("/bill/index", name="listaParagonow")
     */
    public function index (Request $request, DataTableFactory $dataTableFactory) {
        $table = $dataTableFactory->create()
            ->add('id', NumberColumn::class, [
                'label' => 'ID'
            ])
            ->add('shop', TextColumn::class, [
                'label' => 'Sklep'
            ])
            ->add('date', DateTimeColumn::class, [
                'label' => 'Data',
                'format' => 'Y-m-d'
            ])
            ->add("actions", TextColumn::class, [
                'label' => "Opcje",
                'render' => function ($value, $context) {
                    return sprintf('<a class="table-action" href="/edit/%s">%s</a>', $context->getId(), "Edit");
                }
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Bill::class,
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render('bill/index.html.twig', ['datatable' => $table]);
    }
    
    /**
     * Nowy paragon
     * 
     * @Route("/new", name="nowyParagon")
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
