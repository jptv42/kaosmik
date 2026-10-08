<?php

namespace App\Models;

use App\Entities\ChatMessage;
use CodeIgniter\Model;

class ChatMessageModel extends Model
{
    protected $table            = 'chat_messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = chatMessage::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_sender','id_receiver','message'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Récupère l'historique des messages échangés entre deux utilisateurs avec pagination.
     *
     * Cette méthode gère une requête bidirectionnelle (messages envoyés par A à B OU par B à A),
     * trie les résultats par date décroissante, applique une pagination, et inverse l'ordre
     * du tableau de données pour l'affichage chronologique du chat.
     *
     * @param int $id_sender L'identifiant de l'expéditeur.
     * @param int $id_receiver L'identifiant du destinataire.
     * @param int $page Le numéro de la page actuelle (vaut 1 par défaut).
     * @return array Un tableau contenant les messages inversés ('data') et le nombre total de pages ('max_page').
     */
    public function getConversation(int $id_sender, int $id_receiver, int $page = 1){
        // Construction de la requête SQL pour trouver les messages dans les deux sens :
        // (Envoyé par l'expéditeur AU destinataire) OU (Envoyé par le destinataire À l'expéditeur)
        $data = $this->groupStart()
            // Premier bloc : Recherche les messages envoyés par l'expéditeur vers le destinataire
            ->where('id_sender', $id_sender)
            ->where('id_receiver', $id_receiver)
            ->groupEnd()
            ->orGroupStart()
            // Deuxième bloc (OU) : Recherche les messages envoyés par le destinataire vers l'expéditeur
            ->where('id_sender', $id_receiver)
            ->where('id_receiver', $id_sender)
            ->groupEnd()
            // Trie les résultats du plus récent au plus ancien (nécessaire pour la pagination par le bas/fin)
            ->orderBy('created_at', 'DESC')
            // Découpe les résultats par paquets de 10 messages pour la page actuelle demandée
            ->paginate(10, 'default', $page);

        // Retourne un tableau associatif prêt à être utilisé par le contrôleur
        return [
            // Inverse le tableau : on récupère en DESC pour charger les plus récents,
            // mais on les inverse array_reverse() pour que le plus ancien s'affiche en haut et le plus récent en bas.
            'data' => array_reverse($data),

            // Récupère le nombre total de pages disponibles via le gestionnaire de pagination (pager)
            'max_page' => $this->pager->getPageCount()
        ];
    }

    /**
     * Récupère les nouveaux messages reçus d'un utilisateur spécifique après une date donnée.
     *
     * Cette méthode est idéale pour les requêtes AJAX périodiques : elle vérifie si l'interlocuteur
     * a envoyé de nouveaux messages depuis la dernière vérification de l'utilisateur connecté.
     *
     * @param int $sender_id L'identifiant de l'utilisateur connecté (qui attend les messages).
     * @param int $receiver_id L'identifiant de l'interlocuteur (qui a potentiellement envoyé les messages).
     * @param string $date La date/heure de référence (ex: la date du dernier message affiché à l'écran).
     * @return array Un tableau contenant la liste des nouveaux messages triés par ordre chronologique.
     */
    public function getNewMessages(int $sender_id, int $receiver_id, $date){
        // Requête pour chercher les messages en attente
        $newMessages = $this->where('id_sender', $receiver_id)       // 1. Envoyés par l'interlocuteur...
        ->where('id_receiver', $sender_id)                          // 2. ...à destination de l'utilisateur connecté
        ->where('created_at >', $date)                             // 3. ...créés après la date de référence fournie
        ->orderBy('created_at', 'ASC')               // 4. Du plus ancien au plus récent (ordre de lecture naturel)
        ->findAll();                                                 // 5. Exécute la requête et récupère tous les résultats

        // Renvoie le tableau des nouveaux messages trouvés
        return $newMessages;
    }
}