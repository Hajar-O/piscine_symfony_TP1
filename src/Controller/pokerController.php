<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class pokerController extends AbstractController{

    #[Route('/poker', name: 'poker')]

public function poker(){

        $request = Request::createFromGlobals();
        $age = $request->query->get('age');

        if(empty($age)){
            return $this->render('page/poker.html.twig');
        }else {
            if ($age >= 18) {
                return $this->render('page/poker_welcome.html.twig');
            } else {
                return $this->render('page/get_out.html.twig');
            }
        }

    }
}