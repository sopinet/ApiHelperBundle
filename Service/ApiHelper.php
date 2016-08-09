<?php

namespace Sopinet\ApiHelperBundle\Service;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\Translator;

class ApiHelper
{
    /**
     * Constants
     */
    const OK = "Ok";

    const STATE_OK = 1;
    const STATE_ERROR = -1;

    /**
     * ApiHelper constructor.
     * @param ViewHandler $viewHandler
     * @param Translator $translator
     */
    public function __construct(ViewHandler $viewHandler, Translator $translator)
    {
        $this->viewhandler = $viewHandler;
        $this->translator = $translator;
    }

    /**
     * responseOk
     *
     * Crea y devuelve una respuesta de aceptación de la API.
     * Recibe en $data el conjunto de datos que devolverá la API.
     * Opcionalmente, se pueden especificar grupos para el contexto de la serialización.
     * Si no se especificam, el estado HTTP y el mensaje de error tendrán valores por defecto.
     *
     * @param $data
     * @param string $groups
     * @param string $message
     * @param int $httpStatusCode
     * @return Response
     */
    public function responseOk ($data = null, $groups = "", $message = "", $httpStatusCode = Response::HTTP_ACCEPTED)
    {
        $msg = $this->translator->trans($message);

        $response['state'] = $this::STATE_OK;
        $response['msg'] = strlen($msg) ? $msg : $this::OK;
        $response['data'] = $data;

        $view = View::create()
            ->setStatusCode($httpStatusCode)
            ->setData($response);

        if ((is_string($groups) and strlen($groups)) or is_array($groups)) {
            $groupsArray = (array) $groups;
            $view->setSerializationContext(SerializationContext::create()->setGroups($groupsArray));
        }

        return $this->viewhandler->handle($view);
    }
    /** Compatibilidad hacia atrás **/
    public function msgOk ($data = null, $groups = "", $message = "", $httpStatusCode = Response::HTTP_ACCEPTED)
    {
        return $this->responseOk ($data, $groups, $message, $httpStatusCode);
    }    

    /**
     * responseDenied
     *
     * Crea y devuelve una respuesta de denegación de la API.
     * Si no se proporcionan parámetros, el estado HTTP y el mensaje de error tendrán valores por defecto.
     *
     * @param string $message
     * @param int $httpStatusCode
     * @return Response
     */
    public function responseDenied ($message = "", $httpStatusCode = Response::HTTP_NOT_FOUND)
    {
        $response['state'] = $this::STATE_ERROR;
        $response['msg'] = $this->translator->trans($message);

        $view = View::create()
            ->setStatusCode($httpStatusCode)
            ->setData($response);

        return $this->viewhandler->handle($view);
    }
    /** Compatibilidad hacia atrás **/
    public function msgDenied ($message = "", $httpStatusCode = Response::HTTP_NOT_FOUND)
    {
        return $this->responseDenied ($message, $httpStatusCode);
    }

    /**
     * Se hace un submit de los campos de un request que esten definidos en un formulario
     * @param Request $request
     * @param Form $form
     * @return Form
     */
    public function handleForm(Request $request,Form $form){
        // The JSON PUT data will include all attributes in the entity, even
        // those that are not updateable by the user and are not in the form.
        // We need to remove these extra fields or we will get a
        // "This form should not contain extra fields" Form Error
        $data = $request->request->all();
        $children = $form->all();
        //Eliminamos los datos del request que no pertenecen al formulario
        $data = array_intersect_key($data, $children);
        $form->submit($data);
        return $form;
    }

    /**
     * Dado un formulario se devuelven sus errores parseados
     * @param Form $form
     * @param bool $deep option for Form getErrors method
     * @param bool $flatten option for Form getErrors method
     * @return array
     */
    public function getFormErrors(Form $form, $deep=false,$flatten=true){
        // Se parsean los errores que existan en el formulario para devolverlos en el reponse
        $errors=array();
        //Se parsean los posibles errores generales del formulario(incluyendo los asserts a nivel de entidad)
        foreach ($form->getErrors($deep, $flatten) as $key => $error) {
            if ($form->isRoot()) {
                $errors['form'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }
        $childs=$form->getIterator();
        //Se parsean los posibles errores de cada campo del formulario
        /** @var Form $child */
        foreach($childs as $child ){
            $fieldErrors=$child->getErrors();
            while($fieldErrors->current()!=null){
                $errors[$child->getName()][]=$fieldErrors->current()->getMessage();
                $fieldErrors->next();
            }
        }
        return $errors;
    }

    /**
     * Maneja excepciones para devolverlas mediante la API
     * @param \Exception $e
     * @return mixed
     */
    public function handleException(\Exception $e)
    {
        return $this->msgDenied($e->getMessage());
    }

    /**
     * @return mixed
     */
    public function msgDenied($msg=null, $status=200)
    {
        $view = view::create()
            ->setStatusCode($status)
            ->setData($this->doDenied($msg));

        return $this->viewhandler->handle($view);
    }

    /**
     * Funcion para representar un uso erroneo de la API
     *
     * @param String $msg mensaje
     * @return Array $array mensaje con el estado
     */
    private function doDenied($msg=null)
    {
        $array['state'] = -1;

        if ($msg!=null) {
            $array['msg'] = $msg;
        }
        else {
            $array['msg'] = "Access denied";
        }

        return $array;
    }
}
