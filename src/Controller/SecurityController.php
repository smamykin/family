<?php


namespace App\Controller;

use App\Controller\Traits\SaveSubscription;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\UserType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    use SaveSubscription;

    /**
     * @Route("/register/{plan}", name="register", defaults={"plan": null})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SessionInterface $session
     * @param $plan
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, SessionInterface $session, $plan)
    {
        if ($request->isMethod('GET')) {
            $session->set('planName', $plan);
            $session->set('planPrice', Subscription::getPlanDataPriceByName($plan));
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->get('user');
            $plainPassword = $formData['password']['first'];
            $user->setName($formData['name'])
                ->setLastName($formData['last_name'])
                ->setEmail($formData['email'])
                ->setPassword(
                    $passwordEncoder->encodePassword($user, $plainPassword)
                )
                ->setRoles(['ROLE_USER'])
            ;

            $date = new \DateTime();
            $date->modify('+1 month');
            $subscription = new Subscription();
            $subscription->setValidTo($date)
                ->setPlan($session->get('planName'));

            if ($plan == Subscription::getPlanDataNameByIndex(0)) {
                $subscription->setFreePlanUsed(true)
                    ->setPaymentStatus('paid');
            }
            $user->setSubscription($subscription);

            $objectManager = $this->getDoctrine()->getManager();
            $objectManager->persist($user);
            $objectManager->flush();

            $this->loginUserAutomatically($user, $plainPassword);

            return $this->redirectToRoute('admin_main_page');
        }

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED') && $plan == Subscription::getPlanDataNameByIndex(0)) {
            $this->saveSubscription($plan, $this->getUser());
            return $this->redirectToRoute('admin_main_page');
        } elseif ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('payment');
        }
        return $this->render('front/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $helper
     * @return Response
     */
    public function login(AuthenticationUtils $helper)
    {
        return $this->render('front/login.html.twig', [
            'error' => $helper->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     * @throws Exception
     */
    public function logout(): void
    {
        throw new Exception('This should never be reached!');
    }

    private function loginUserAutomatically(User $user, $password)
    {
        $token = new UsernamePasswordToken($user, $password, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

    }
}
