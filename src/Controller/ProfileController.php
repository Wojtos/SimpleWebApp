<?php


namespace App\Controller;


use App\DTO\ProfileRequest;
use App\Entity\Profile;
use App\Form\ProfileRequestType;
use App\Repository\ProfileRepository;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileController extends AbstractController
{
    const PER_PAGE = 10;

    /**
     * @Route("/", name="app_display_form")
     */
    public function displayForm(): Response {
        $form = $this->createForm(ProfileRequestType::class);

        return $this->render(
            'profile/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/send-form", name="app_send_form")
     */
    public function sendForm(Request $request, ValidatorInterface $validator, FileUploader $fileUploader): Response {
        $profileRequest = new ProfileRequest($request);
        $violations = $validator->validate($profileRequest);
        if ($violations->count() > 0) {
            $violation = $violations->get(0);
            return new JsonResponse([
                'error' => $violation->getPropertyPath() . ': ' .$violation->getMessage()
            ],
                400);
        }
        try {
            $profile = Profile::fromRequestAndFileUploader($profileRequest, $fileUploader);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($profile);
            $entityManager->flush();
        } catch (FileException $exception) {
            return new JsonResponse([
                'error' => 'Error during saving a file on disk'
            ],
            500);
        }

        return new JsonResponse([
            'message' => 'Success'
        ]);
    }

    /**
     * @Route("/profiles", name="app_profiles")
     */
    public function profiles(Request $request, PaginatorInterface $paginator, ProfileRepository $profileRepository): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $pagination = $paginator->paginate(
            $profileRepository->createFindAllQuery(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('profile/profiles.html.twig', ['pagination' => $pagination]);
    }
}