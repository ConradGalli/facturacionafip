<?php

/**
 * contacto actions.
 *
 * @package    facturacionafip
 * @subpackage contacto
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class contactoActions extends sfActions
{
  public function executeShow(sfWebRequest $request)
  {
    $this->contacto = ContactoPeer::retrieveByPk($request->getParameter('id'));
    $this->forward404Unless($this->contacto);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->cliente = ClientePeer::retrieveByPk($request->getParameter('cliente_id'));
    $this->forward404Unless($this->cliente);

    $this->form = new ContactoForm();
    
    // Agregar el id del cliente al formulario
    $this->form->setDefault('cliente_id', $this->cliente->getId());
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));
    $this->form = new ContactoForm();
    $this->processForm($request, $this->form, 'creado');
    $this->cliente = ClientePeer::retrieveByPk($this->form['cliente_id']->getValue());

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($contacto = ContactoPeer::retrieveByPk($request->getParameter('id')), sprintf('Object contacto does not exist (%s).', $request->getParameter('id')));
    $this->cliente = $contacto->getCliente();
    $this->form = new ContactoForm($contacto);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $this->forward404Unless($contacto = ContactoPeer::retrieveByPk($request->getParameter('id')), sprintf('Object contacto does not exist (%s).', $request->getParameter('id')));
    $this->cliente = $contacto->getCliente();

    $this->form = new ContactoForm($contacto);

    $this->processForm($request, $this->form, 'actualizado');

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
//    $request->checkCSRFProtection();

    $this->forward404Unless($contacto = ContactoPeer::retrieveByPk($request->getParameter('id')), sprintf('Object contacto does not exist (%s).', $request->getParameter('id')));

    $cliente = $contacto->getCliente();
    $contacto->delete();

    $this->messageBox = new MessageBox("success" , "El contacto fué borrado correctamente", $this->getUser());

    $this->redirect('cliente/show?id='.$cliente->getId());
  }

  protected function processForm(sfWebRequest $request, sfForm $form, $accion)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {

      $cliente = ClientePeer::retrieveByPk($form->getValue('cliente_id'));
      
      $this->forward404Unless($cliente, sprintf('Cliente no encontrado (%s).', $form->getValue('cliente_id')));	
      
      $contacto = $form->save();
      $this->messageBox = new MessageBox("success", "El contacto ha sido $accion correctamente", $this->getUser());	


      $this->redirect('cliente/show?id='.$contacto->getCliente()->getId());
    }else{
      $this->messageBox = new MessageBox("error", "Verifique los datos ingresados", $this->getUser());
    }
  } # processForm
} # actions
