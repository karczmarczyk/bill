<?php
namespace App\Controller;

use App\Entity\BillScan;
use App\Form\BillScanType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Bill;
use App\Form\BillType;
use App\Form\BillWithScansType;
use Doctrine\Common\Collections\ArrayCollection;
use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\ActionColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Description of BillController
 *
 * @author mateusz
 */
class BillController extends AbstractController
{
    
    /**
     * Lista paragonów
     *
     * @Route("/")
     * @Route("/bill/index", name="listaParagonow")
     */
    public function index(Request $request, DataTableFactory $dataTableFactory)
    {
        $table = $dataTableFactory->create(['searching'=>true])
            ->add('id', NumberColumn::class, [
                'label' => 'ID'
            ])
            ->add('shop', TextColumn::class, [
                'label' => 'Sklep',
                'searchable' => true, 'globalSearchable' => true, 'filter' => []
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
     * Zwraca obrazek
     * @Route("/bill/image/{id}/{hash}", name="paragonImg")
     */
    public function billImage($id, $hash)
    {
        $publicResourcesFolderPath = $this->getParameter('bill_directory');
        $filename = $hash;
        return new BinaryFileResponse($publicResourcesFolderPath.$filename);
    }

    /**
     * Nowy paragon
     *
     * @Route("/new", name="nowyParagon")
     */
    public function newBill(Request $request)
    {
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
     * @Route("/edit/{id}", name="edytujParagon")
     */
    public function editBill(Request $request, FileUploader $fileUploader, $id)
    {
        $bill = $this->getDoctrine()
            ->getRepository(Bill::class)
            ->find($id);

        $originalPositions = new ArrayCollection();
        foreach ($bill->getPositions() as $position) {
            $originalPositions->add($position);
        }
        $originalBillScans = new ArrayCollection();
        foreach ($bill->getBillScans() as $billScan) {
            $originalBillScans->add($billScan);
        }

        $form = $this->createForm(BillWithScansType::class, $bill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bill = $form->getData();

            //Przetwarzam uploadowane pliki

            $billScans = $form->get('billScans')->all();
            if ($billScans) {
                $bill->clearUnsaved();
                foreach ($billScans as $billScan) {
                    $file = $billScan->get('billFile')->getData();
                    if ($file) {
                        $originalFilename = $file->getClientOriginalName();
                        $safeFileName = $fileUploader->upload($file);
                        if ($safeFileName) {
                            $newBillScan = new BillScan();
                            $newBillScan->setFileName($safeFileName);
                            $newBillScan->setFileNameOrig($originalFilename);
                            $bill->addBillScan($newBillScan);
                        }
                    }
                }
            }

            $entityManager = $this->getDoctrine()->getManager();

            foreach ($originalPositions as $position) {
                if (false === $bill->getPositions()->contains($position)) {
                    $bill->removePosition($position);
                    $entityManager->persist($position);
                }
            }
            foreach ($originalBillScans as $billScan) {
                if (false === $bill->getBillScans()->contains($billScan)) {
                    $bill->removeBillScan($billScan);
                    $entityManager->persist($billScan);
                }
            }

            
            $entityManager->persist($bill);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Zapisano paragon!'
            );
            return $this->redirect("/edit/".$id);
        }

        return $this->render('bill/edit.html.twig', [
            'form' => $form->createView(),
            'bill' => $bill,
            'id' => $id
        ]);
    }

    /**
     * Edycja paragonu 2
     *
     * @Route("/edit2/{id}")
     */
    public function edit2Bill(Request $request, $id)
    {
        $bill = $this->getDoctrine()
            ->getRepository(Bill::class)
            ->find($id);

        $originalPositions = new ArrayCollection();
        foreach ($bill->getPositions() as $position) {
            $originalPositions->add($position);
        }

        $form = $this->createForm(BillType::class, $bill);
        $form->handleRequest($request);

        $formDropFile = $this->createForm(BillScanType::class, new BillScan());

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
            return $this->redirect("/edit2/".$id);
        }

        return $this->render('bill/edit2.html.twig', [
            'form' => $form->createView(),
            'formDropFile' => $formDropFile->createView(),
            'bill' => $bill,
            'id' => $id
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @Route("uploadScan/{id}", name="uploadScan", methods={"POST"})
     */
    public function uploadScan(Request $request, $id)
    {
        $bill = $this->getDoctrine()
            ->getRepository(Bill::class)
            ->find($id);

        var_dump($_POST);
        var_dump($_FILES);

        $form = $this->createForm(BillScanType::class, new BillScan());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            echo "OK";
            exit;
        } else {
            echo "ERROR ".count($form->getErrors());
            foreach ($form->getErrors() as $error) {
                echo $error->getMessage();
                echo "<br>";
            }
        }

        return new Response();
    }
}
