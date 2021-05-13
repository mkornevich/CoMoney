<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserRegisterType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{id<\d+>}", name="user_profile")
     */
    public function profile(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/list", name="user_list")
     */
    public function list(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $qb = $userRepository->createQueryBuilder('u');
        $query = $qb->getQuery();
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 10);
        return $this->render('user/list.html.twig', [
            'users' => $query->execute(),
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/user/{id<\d+>}/edit", name="user_edit")
     */
    public function edit(int $id, Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $userRepository->find($id);
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form['password']->getData();
            if ($newPassword === '') {
                $user->setPassword($passwordEncoder->encodePassword($user, $newPassword));
            }

            $newRole = $form['role']->getData();
            $user->setRoles($newRole === 'admin' ? ['ROLE_USER', 'ROLE_ADMIN'] : ['ROLE_USER']);

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_list');
        }

        $form->get('role')->setData(in_array('ROLE_ADMIN', $user->getRoles()) ? 'admin' : 'user');

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $form->get('password')->getData());
            $user->setPassword($password);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}