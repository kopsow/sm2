<?php
namespace Application\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use DOMPDFModule\View\Model\PdfModel;

class PdfController extends AbstractActionController {
  
    private $registrationTable;
    
    public function getRegistrationTable()
    {
        if (!$this->registrationTable) {
            $sm = $this->getServiceLocator();
            $this->registrationTable = $sm->get('Registration\Model\RegistrationTable');
        }
        return $this->registrationTable;
    }
    public function __construct() {
        $this->session = new \Zend\Session\Container('login');
    }
     public function indexAction() {
         // Instantiate new PDF Model
         $pdf = new PdfModel();
          
         // set filename
         $pdf->setOption('filename', 'lista_wizyt.pdf');
          
         // Defaults to "8x11"
         $pdf->setOption('paperSize', 'a4');
          
         // paper orientation
         $pdf->setOption('paperOrientation', 'portrait');
          
         $pdf->setVariables(array(
             'var1' => $this->getRegistrationTable()->listRegistration()
         ));
          
         return $pdf;
     }
 }