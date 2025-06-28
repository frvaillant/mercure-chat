<?php

namespace App\Controller;

use App\Entity\MercureTest;
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageTypeForm;
use App\Repository\MercureTestRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\Conversation\ConversationDataTransformer;
use App\Service\Conversation\MessageBuilder;
use App\Service\Mercure\IsTypingNotifier;
use App\Service\Mercure\MessageNotifier;
use App\Service\Mercure\Testing\MercureTestService;
use App\Service\Mercure\TestNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    #[IsGranted('ROLE_USER')]
    public function home(
        UserRepository $userRepository,
        MercureTestRepository $mercureTestRepository
    ): Response {

        $hasBeenTested = count($mercureTestRepository->findBy([
                'user' => $this->getUser()
            ])) > 0;

        $users = $userRepository->findBy(
            [],
            ['username' => 'ASC']
        );

        return $this->render('home/index.html.twig', [
            'from' => null,
            'users' => $users,
            'has_been_tested' => $hasBeenTested
        ]);
    }


    #[Route('/conversation/{conversationWith}', name: 'app_conversation')]
    #[IsGranted('ROLE_USER')]
    public function index(
        MessageRepository $messageRepository,
        UserRepository $userRepository,
        User $conversationWith,
        MessageBuilder $messageBuilder
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $messageBuilder->buildMessage($user, $conversationWith);

        $form = $this->createForm(MessageTypeForm::class, $messageBuilder->getMessage());

        $conversation = $messageRepository->getConversationBetween($user, $conversationWith);

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'conversation' => $conversation,
            'conversation_data' => ConversationDataTransformer::getConversationData($conversation, $user),
            'from' => $conversationWith,
            'users' => $userRepository->findBy( [], ['username' => 'ASC'] )
        ]);
    }


    #[Route('/send-message-to/{conversationWith}', name: 'app_send_message', methods: ['POST'])]
    public function sendMessage(
        Request $request,
        User $conversationWith,
        MessageNotifier $messageNotifier,
        MessageBuilder $messageBuilder
    ): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $messageBuilder->buildMessage($user, $conversationWith);

        $form = $this->createForm(MessageTypeForm::class, $messageBuilder->getMessage());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($messageBuilder->finalize($form->get('text')->getData())) {

                $messageNotifier->notifyMessage($messageBuilder->getMessage());

                return new JsonResponse([
                    'message' => ConversationDataTransformer::getMessageData($messageBuilder->getMessage(), $user)
                ], Response::HTTP_OK);
            }

        }

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    #[Route('/messages-with-as-json/{conversationWith}', name: 'app_messages_with_as_json')]
    #[IsGranted('ROLE_USER')]
    public function messagesBetweenAsJson(
        MessageRepository $messageRepository,
        User $conversationWith,
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $conversation = $messageRepository->getConversationBetween($user, $conversationWith);

        $conversationData = ConversationDataTransformer::getConversationData($conversation, $user, true);

        return new JsonResponse(['conversation' => $conversationData], Response::HTTP_OK);

    }

    #[Route('/notify-typing/{targetUser}/{mode}', name: 'app_message_typing')]
    #[IsGranted('ROLE_USER')]
    public function messagesTyping(
        Request $request,
        IsTypingNotifier $notifier,
        User $targetUser,
        string $mode
    ): RedirectResponse | JsonResponse
    {

        if($request->getMethod() === 'GET') {
            return $this->redirectToRoute('app_home');
        }
        /** @var User $user */
        $user = $this->getUser();
        $notifier->notifyIsTyping($user, $targetUser, $mode);

        return new JsonResponse(['success' => true], Response::HTTP_OK);
    }

    #[Route('/test_mercure/{do}', name: 'app_test_m')]
    public function test(
        TestNotifier $testNotifier,
        MercureTestService $mercureTestService,
        int $do = 0
    ): JsonResponse
    {

        /** @var User $user */
        $user = $this->getUser();

        if($mercureTestService->hasBeenTestedBy($user)) {

            if($do === 0) {
                return new JsonResponse(['hasBeenTested' => true], Response::HTTP_OK);
            }

            $mercureTestService->removeTestsBy($user);

        }

        $testNotifier->notifyTest($user->getId(), $mercureTestService->saveHasBeenTestedBy($user));
        return new JsonResponse(['success' => true], Response::HTTP_OK);


    }

}
