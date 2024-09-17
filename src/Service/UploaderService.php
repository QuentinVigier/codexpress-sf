<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Service de téléversement de fichier dans l'application CodeXpress
 * - Images (.jpg, .jpeg, .png, .gif)
 * - Documents (Plus tard)
 * 
 *  Méthodes: Téléverser, Supprimer
 */
class UploaderService
{
    private $param;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->param = $parameterBag;
    }

    public function uploadImage($file): string
    {
        try {
            // $orignalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = uniqid('image-') . '.' . $file->guessExtension();
            $file->move($this->param->get('uploads_images_directory'), $fileName);

            return $this->param->get('uploads_images_directory') . '/' . $fileName;
        } catch (\Exception $e) {
            throw new \Exception('An error occured while uploading the image: ' . $e->getMessage());
        }
    }
}
