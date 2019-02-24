<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {

        return $this->render('home/index.html.twig', [
            'token' => $this->createToken(),
        ]);
    }

    /**
     * @Route("/home/login", name="Home.login", methods={"POST"})
     */
    public function login(Request $request){
        $tok=$request->get("tok");
        if ($tok!=$this->createToken()){
            return $this->json(["data"=>"","err"=>"token"]);
        }
        else{
            $id=$request->get("id");
            $mdp=$request->get("mdp");
            $idTrouvee=$this->getDoctrine()->getRepository(User::class)->findOneBy(["identifiant"=>$id]);
            if ($idTrouvee==null){
                return $this->json(["data"=>"","err"=>"id"]);
            }
            else{
                if ($mdp!=$idTrouvee->getMotDePasse()){
                    return $this->json(["data"=>"","err"=>"mdp"]);
                }
                else{
                    return $this->json(["user"=>$idTrouvee]);
                }
            }
        }
    }

    /**
     * @Route("/home/register", name="Home.register", methods={"POST"})
     */
    public function register(Request $request, ObjectManager $manager){
        $identifiant=$request->get("id");
        $mdp=$request->get("mdp");
        $newUser=new User();
        $newUser->setIdentifiant($identifiant);
        $newUser->setMotDePasse($mdp);
        $manager->persist($newUser);
        $manager->flush();
        return $this->json(["data"=>$newUser]);
    }

    /**
     * @Route("/home/getToken", name="Home.getTocken", methods={"GET"})
     */
    public function getToken(){
       return $this->json(["tok"=>$this->createToken()]);
    }

    private function createToken(){
        return sha1(date("d/m/y"));
    }
}
