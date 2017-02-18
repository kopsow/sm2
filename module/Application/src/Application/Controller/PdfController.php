<?php
namespace Application\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use DOMPDFModule\View\Model\PdfModel;

class PdfController extends AbstractActionController {
  
     public function indexAction() {
         // Instantiate new PDF Model
         $pdf = new PdfModel();
          
         // set filename
         $pdf->setOption('filename', 'hello.pdf');
          
         // Defaults to "8x11"
         $pdf->setOption('paperSize', 'a4');
          
         // paper orientation
         $pdf->setOption('paperOrientation', 'portrait');
          
         $pdf->setVariables(array(
             'var1' => 'Liverpool FC',
             'var2' => 'Atletico Madrid',
             'var3' => 'Borussia Dortmund'
         ));
          
         return $pdf;
     }
 }