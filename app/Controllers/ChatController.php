<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ChatController extends BaseController
{
    protected $current_menu = 'chat';
    protected $title = 'Chat';

    protected $chatMessageModel;
    protected $userModel;
    public function __construct() {
        $this->chatMessageModel = model('ChatMessageModel');
        $this->userModel = model('UserModel');
    }
    public function index()
    {
        $users = $this->userModel->where('id !=', auth()->getUser()->id)->findAll();
        $final_array = array();
        foreach($users as $user) {
            $conversation = $this->chatMessageModel->getConversation($user->id, auth()->getUser()->id);
            $lastMessage = !empty($conversation['data']) ? end($conversation['data']) : null;
            $final_array[] = [
                'user' => $user,
                'last_message' => $lastMessage
            ];
        }
        return $this->render('front/chat/choice-receiver', ['users' => $final_array]);
    }

    /**
     * Affiche l'historique d'une conversation privée entre l'utilisateur connecté et un destinataire.
     *
     * Cette méthode récupère le profil du destinataire via son nom d'utilisateur, vérifie son existence,
     * récupère les messages échangés (avec pagination) entre les deux utilisateurs, et charge la vue
     * correspondante avec toutes les données nécessaires.
     *
     * @param string $receiver_username Le nom d'utilisateur du destinataire de la conversation.
     * @return mixed Redirection en cas d'erreur ou rendu de la vue du chat avec les messages.
     */
    public function conversation($receiver_username) {
        // Recherche le destinataire dans la base de données grâce à son pseudo (username)
        $receiver = $this->userModel->where('username', $receiver_username)->first();

        // Si l'utilisateur n'existe pas, génère une erreur et redirige vers la page principale du chat
        if(empty($receiver)) {
            $this->error('Utilisateur introuvable');
            return $this->redirect('/chat');
        }

        // Récupère l'ID du destinataire trouvé et l'ID de l'expéditeur (l'utilisateur actuellement connecté)
        $id_receiver = $receiver->id;
        $id_sender = auth()->getUser()->id;

        // Récupère les messages de la conversation ainsi que les informations de pagination via le modèle
        $data = $this->chatMessageModel->getConversation($id_sender, $id_receiver);

        // Charge et affiche la vue du chat en lui transmettant les messages, la pagination et les infos du destinataire
        return $this->render('front/chat/chat', [
            'messages' => $data['data'],
            'max_page' => $data['max_page'],
            'receiver' => $receiver
        ]);
    }

    public function send() {
        if(!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Requêtes invalide']);
        }
        $sender_id = auth()->getUser()->id;
        $receiver_id = $this->request->getPost('receiver_id');
        $message = $this->request->getPost('message');

        if(empty($message) || empty($receiver_id)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Le message ou le destinataire est manquant',
            ]);
        }

        $data = [
            'id_sender' => $sender_id,
            'id_receiver' => $receiver_id,
            'message' => $message,
        ];

        $messageId = $this->chatMessageModel->insert($data);
        if($messageId) {
            $newMessage = $this->chatMessageModel->find($messageId);

            $htmlBubble = view_cell('BubbleMessageCell', [
                'chatMessage' => $newMessage,
                'sender_context' => true,
            ]);

            return $this->response->setJSON([
                'success' => true,
                'html' => $htmlBubble,
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'error' => 'Erreur lors de la sauvegarde'
        ]);
    }

    public function newMessages() {
        //vérifie en ajax
        if(!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Requêtes invalide']);
        }
        $sender_id = auth()->getUser()->id;
        $receiver_id = $this->request->getGet('receiver_id');
        $lastDate = $this->request->getGet('last_date');

        //si pas d'id ou pas de dernière date
        if(empty($receiver_id) || empty($lastDate)) {
            return $this->response->setJSON([
                //renvoi d'une erreur si vide
                'success' => false,
                'messages'=>[]
            ]);
        }
        $newMessages = $this->chatMessageModel->getNewMessages($sender_id, $receiver_id, $lastDate);

        //si les messages sont vides on ne remplis pas de bulle
        $htmlBubbles='';
        $latestDate = $lastDate;
        //s'il y a plusieurs messages
        foreach($newMessages as $message) {
            $htmlBubble = view_cell('BubbleMessageCell', [
                'chatMessage' => $message,
                'sender_context' => false,
            ]);
            $htmlBubbles .= $htmlBubble;
            $latestDate = $message->created_at;
        }
        //renvoi des messages au vue en format html
        return $this->response->setJSON([
            'success' => true,
            'html' => $htmlBubbles,
            'latest_date' => (string) $latestDate,
        ]);
    }
}